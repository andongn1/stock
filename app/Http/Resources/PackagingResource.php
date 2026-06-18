<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackagingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit_id' => $this->unit_id,
            'unit' => $this->whenLoaded('unit'),
            'unit_quantity' => $this->unit_quantity,
            'formatted_unit' => $this->whenLoaded('unit', function () {
                return $this->unit_quantity . ' ' . $this->unit?->name;
            }),
            'articles_count' => $this->whenCounted('articles'),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
