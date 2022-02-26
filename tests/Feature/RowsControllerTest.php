<?php

namespace Tests\Feature;

use App\Imports\RowsImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class RowsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected const ROWS_FILE_FIXTURE_PATH = __DIR__ . '/../Fixtures/test.xlsx';

    /** @test */
    public function a_user_can_import_rows(): void
    {
        Excel::fake();

        $filename = basename(self::ROWS_FILE_FIXTURE_PATH);
        $file = new UploadedFile(static::ROWS_FILE_FIXTURE_PATH, $filename, 'application/excel', null, true);
        $this->postJson(route('rows.store'), ['file' => $file])->assertOk();

        Excel::assertQueued($filename, static fn (RowsImport $import): bool => true);
        Excel::assertImported($filename, static fn (RowsImport $import): bool => true);
    }

    /** @test */
    public function a_user_can_view_rows(): void
    {
        $this->getJson(route('rows.index'))->assertOk();
    }
}
