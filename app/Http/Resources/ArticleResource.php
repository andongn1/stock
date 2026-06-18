<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'brand' => $this->brand,
            'family_id' => $this->family_id,
            'sub_family_id' => $this->sub_family_id,
            'unit_id' => $this->unit_id,
            'packaging_id' => $this->packaging_id,
            'vat_id' => $this->vat_id,
            'family' => $this->whenLoaded('family', function () {
                return [
                    'id' => $this->family->id,
                    'name' => $this->family->name,
                ];
            }),
            'sub_family' => $this->whenLoaded('subFamily', function () {
                return [
                    'id' => $this->subFamily->id,
                    'name' => $this->subFamily->name,
                ];
            }),
            'unit' => $this->whenLoaded('unit', function () {
                return [
                    'id' => $this->unit->id,
                    'name' => $this->unit->name,
                ];
            }),
            'packaging' => $this->whenLoaded('packaging', function () {
                return [
                    'id' => $this->packaging->id,
                    'name' => $this->packaging->name,
                    'unit_id' => $this->packaging->unit_id,
                    'unit' => $this->packaging->unit?->only('id', 'name'),
                    'unit_quantity' => $this->packaging->unit_quantity,
                    'formatted_unit' => $this->packaging->unit_quantity . ' ' . ($this->packaging->unit?->name ?? ''),
                ];
            }),
            'vat' => $this->whenLoaded('vat', function () {
                return [
                    'id' => $this->vat->id,
                    'rate' => $this->vat->rate,
                    'label' => $this->vat->label,
                ];
            }),
            'current_price' => $this->whenLoaded('currentPrice', function () {
                return $this->currentPrice ? [
                    'price' => $this->currentPrice->price,
                    'currency' => $this->currentPrice->currency,
                    'supplier' => $this->currentPrice->supplier->name,
                    'valid_from' => $this->currentPrice->valid_from->toDateString(),
                ] : null;
            }),
            'price_histories_count' => $this->whenCounted('priceHistories'),
            'price_histories' => PriceHistoryResource::collection($this->whenLoaded('priceHistories')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
