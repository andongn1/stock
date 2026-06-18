<?php

namespace App\Models;

use App\Enums\PriceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'supplier_id',
        'price',
        'currency',
        'price_type',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_type' => PriceType::class,
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    /**
     * Get the article this price history belongs to.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the supplier this price history belongs to.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the unit price based on the price type.
     * If price is per_unit, returns the price.
     * If price is per_packaging, calculates price / packaging_unit_quantity.
     */
    public function getUnitPriceAttribute(): float
    {
        if ($this->price_type === PriceType::PER_UNIT) {
            return (float) $this->price;
        }

        // Price is per packaging, calculate unit price
        if ($this->article && $this->article->packaging_unit_quantity > 0) {
            return (float) ($this->price / $this->article->packaging_unit_quantity);
        }

        return (float) $this->price;
    }

    /**
     * Get the packaging price based on the price type.
     * If price is per_packaging, returns the price.
     * If price is per_unit, calculates price * packaging_unit_quantity.
     */
    public function getPackagingPriceAttribute(): float
    {
        if ($this->price_type === PriceType::PER_PACKAGING) {
            return (float) $this->price;
        }

        // Price is per unit, calculate packaging price
        if ($this->article && $this->article->packaging_unit_quantity > 0) {
            return (float) ($this->price * $this->article->packaging_unit_quantity);
        }

        return (float) $this->price;
    }

    /**
     * Get the formatted price type label.
     */
    public function getPriceTypeLabelAttribute(): string
    {
        if ($this->price_type instanceof PriceType) {
            return $this->price_type->label();
        }
        return ucfirst(str_replace('_', ' ', $this->price_type ?? 'per_unit'));
    }
}
