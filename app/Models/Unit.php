<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all articles using this unit.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
