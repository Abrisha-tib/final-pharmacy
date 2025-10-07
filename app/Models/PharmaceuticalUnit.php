<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmaceuticalUnit extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'description',
        'category',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
