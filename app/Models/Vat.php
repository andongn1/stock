<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vat extends Model
{
    use HasFactory;

    protected $fillable = ['rate', 'label'];

    /**
     * Get all articles using this VAT rate.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
