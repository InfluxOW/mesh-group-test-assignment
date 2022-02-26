<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRowsRequest;
use App\Imports\RowsImport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use function config;
use function response;

class RowsController extends Controller
{
    public function index()
    {
        //
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
