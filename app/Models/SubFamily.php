<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubFamily extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'family_id'];

    /**
     * Get the family this sub-family belongs to.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Get all articles for this sub-family.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
