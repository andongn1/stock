<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Packaging extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit_id', 'unit_quantity'];

    /**
     * Get the unit associated with this packaging.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get all articles using this packaging.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
    
    /**
     * Get the formatted unit display (e.g., "10 kg").
     */
    public function getFormattedUnitAttribute(): string
    {
        if ($this->unit && $this->unit_quantity) {
            return $this->unit_quantity . ' ' . $this->unit->name;
        }
        return $this->name;
    }
}
