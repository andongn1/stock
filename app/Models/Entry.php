<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
    use HasFactory;
    
    protected $fillable = ['article_id', 'quantity', 'date'];
    
    protected $casts = [
        'quantity' => 'decimal:4',
        'date' => 'datetime',
    ];
    
    /**
     * Get the article this entry belongs to.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
    
    /**
     * Get the total quantity in base units (quantity * packaging_unit_quantity).
     */
    public function getTotalQuantityAttribute(): float
    {
        if ($this->article && $this->article->packaging_unit_quantity) {
            return (float) ($this->quantity * $this->article->packaging_unit_quantity);
        }
        return (float) $this->quantity;
    }
    
    /**
     * Get the monetary value of this entry (total_quantity * unit_price).
     */
    public function getMonetaryValueAttribute(): float
    {
        if ($this->article && $this->article->currentPrice) {
            return (float) ($this->total_quantity * $this->article->currentPrice->unit_price);
        }
        return 0.0;
    }
}
