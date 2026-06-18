<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceHistoryResource extends JsonResource
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
            'article_id' => $this->article_id,
            'supplier_id' => $this->supplier_id,
            'price' => $this->price,
            'currency' => $this->currency,
            'price_type' => $this->price_type?->value ?? $this->price_type,
            'price_type_label' => $this->price_type_label,
            'unit_price' => $this->unit_price,
            'packaging_price' => $this->packaging_price,
            'valid_from' => $this->valid_from->toDateString(),
            'valid_to' => $this->valid_to?->toDateString(),
            'article' => $this->whenLoaded('article', function () {
                return [
                    'id' => $this->article->id,
                    'name' => $this->article->name,
                    'brand' => $this->article->brand,
                    'packaging_unit_quantity' => $this->article->packaging_unit_quantity,
                    'unit' => $this->article->unit?->only('id', 'name'),
                    'packaging' => $this->article->packaging?->only('id', 'name'),
                ];
            }),
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
