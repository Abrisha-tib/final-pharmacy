<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the items count for this category
     */
    public function getItemsCountAttribute()
    {
        // This will be implemented when we have the Item model
        return 0; // For now, return 0
    }

    /**
     * Get the formatted color with hash
     */
    public function getFormattedColorAttribute()
    {
        return $this->color ? '#' . ltrim($this->color, '#') : '#3B82F6';
    }
}
