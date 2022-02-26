<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRowsRequest;
use App\Http\Resources\RowResource;
use App\Imports\RowsImport;
use App\Models\Row;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;

class RowsController extends Controller
{
    public function index(): JsonResponse
    {
        $page = Paginator::resolveCurrentPage();
        $perPage = 1000;

        $rows = Row::query()
            ->groupBy('date')
            ->orderBy('date')
            ->orderBy('name')
            ->orderBy('id')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->groupBy('date')
            ->map(fn (Collection $rows): JsonResource => RowResource::collection($rows));

        return response()->json(['items' => $rows]);
    }

    public function store(StoreRowsRequest $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file;
        $import = (new RowsImport($file->getClientOriginalName(), Carbon::now()));

        Excel::queueImport($import, $file)->allOnQueue(config('queue.connections.rabbitmq.queue'));

        return response()->json();
    }
}
