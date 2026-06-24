<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;
    
    protected $fillable = ['article_id', 'quantity', 'date'];
    protected $casts = [
        'quantity' => 'decimal:4',
        'date' => 'datetime',
    ];
    
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
    
    public function getTotalQuantityAttribute(): float
    {
        if ($this->article && $this->article->packaging_unit_quantity) {
            return (float) ($this->quantity * $this->article->packaging_unit_quantity);
        }
        return (float) $this->quantity;
    }
    
    public function getMonetaryValueAttribute(): float
    {
        if ($this->article && $this->article->current_price) {
            return (float) ($this->total_quantity * $this->article->current_price->unit_price);
        }
        return 0.0;
    }
}
