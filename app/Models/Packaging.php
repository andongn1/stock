<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Packaging extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all articles using this packaging.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
