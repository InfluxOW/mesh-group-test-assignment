<?php

namespace App\Imports;

use App\Models\Row;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class RowsImport implements ToModel, WithUpserts, WithUpsertColumns, WithBatchInserts, WithValidation, WithChunkReading, ShouldQueue, WithHeadingRow, WithEvents
{
    use RemembersRowNumber;
    use RemembersChunkOffset;

    protected const BATCH_SIZE = 1000;

    protected int $totalRowsNumber;

    public function __construct(protected string $filename, protected Carbon $date)
    {
    }

    public function model(array $row): Row
    {
        $this->updateProgress();

        return new Row([
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'date' => $row['date'],
        ]);
    }

    public function uniqueBy(): array
    {
        return ['id'];
    }

    public function upsertColumns(): array
    {
        return ['name', 'date'];
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'date' => ['required', 'date'],
        ];
    }

    public function prepareForValidation(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'date' => Date::excelToDateTimeObject($row['date']),
        ];
    }

    public function batchSize(): int
    {
        return static::BATCH_SIZE;
    }

    public function chunkSize(): int
    {
        return static::BATCH_SIZE;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event): void {
                $this->totalRowsNumber = (int) array_sum($event->getReader()->getTotalRows());
            },
            AfterImport::class => function (): void {
                $this->finishProgress();
            },
        ];
    }

    private function updateProgress(): void
    {
        $currentBatch = (int)(floor($this->getChunkOffset() / static::BATCH_SIZE));

        Redis::set($this->getCacheKey(), sprintf('%s_%s', $currentBatch * static::BATCH_SIZE, $this->totalRowsNumber));
    }

    private function finishProgress(): void
    {
        Redis::set($this->getCacheKey(), sprintf('%s_%s', $this->totalRowsNumber, $this->totalRowsNumber));
    }

    private function getCacheKey(): string
    {
        return sprintf('%s_%s', $this->filename, Str::kebab($this->date->toDateTimeString()));
    }
}
