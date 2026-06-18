<?php

namespace App\Models;

use App\Enums\SaleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellingPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'price_ttc',
        'sale_type',
        'quantity',
        'currency',
    ];

    protected $casts = [
        'price_ttc' => 'decimal:2',
        'sale_type' => SaleType::class,
        'quantity' => 'decimal:4',
    ];

    /**
     * Get the article this selling price belongs to.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the formatted sale type label.
     */
    public function getSaleTypeLabelAttribute(): string
    {
        if ($this->sale_type instanceof SaleType) {
            return $this->sale_type->label();
        }
        return ucfirst(str_replace('_', ' ', $this->sale_type ?? 'per_unit'));
    }

    /**
     * Get the formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_ttc, 2) . ' ' . ($this->currency ?? 'EUR');
    }
}
