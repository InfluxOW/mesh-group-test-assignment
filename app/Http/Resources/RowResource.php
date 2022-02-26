<?php

namespace App\Http\Resources;

use App\Models\Row;
use Illuminate\Http\Resources\Json\JsonResource;

class RowResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Row $row */
        $row = $this->resource;

        return [
            'id' => $row->id,
            'name' => $row->name,
        ];
    }
}
