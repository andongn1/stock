<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'family_id',
        'sub_family_id',
        'unit_id',
        'packaging_id',
        'packaging_unit_quantity',
        'vat_id',
    ];

    /**
     * Get the family this article belongs to.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Get the sub-family this article belongs to.
     */
    public function subFamily(): BelongsTo
    {
        return $this->belongsTo(SubFamily::class);
    }

    /**
     * Get the unit for this article.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the packaging for this article.
     */
    public function packaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }

    /**
     * Get the VAT rate for this article.
     */
    public function vat(): BelongsTo
    {
        return $this->belongsTo(Vat::class);
    }

    /**
     * Get all price histories for this article.
     */
    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }

    /**
     * Accessor for current_price attribute.
     * Returns the most recent price for this article (by valid_from date).
     */
    public function getCurrentPriceAttribute(): ?PriceHistory
    {
        return $this->priceHistories()
            ->orderBy('valid_from', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }
    
    /**
     * Get the formatted packaging unit info (e.g., "10 kg", "250 pieces").
     */
    public function getPackagingUnitInfoAttribute(): string
    {
        if ($this->packaging_unit_quantity && $this->unit) {
            return $this->packaging_unit_quantity . ' ' . $this->unit->name;
        }
        return '';
    }
}
