<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all sub-families for this family.
     */
    public function subFamilles(): HasMany
    {
        return $this->hasMany(SubFamily::class);
    }

    /**
     * Get all articles for this family.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
