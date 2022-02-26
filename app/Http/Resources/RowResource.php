<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Row;

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
