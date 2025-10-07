<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
        'ip_address',
        'port',
        'capabilities',
        'is_default',
        'description',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'available' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'busy' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'offline' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
            'error' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    /**
     * Get printer type icon
     */
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'laser' => 'fas fa-print',
            'inkjet' => 'fas fa-tint',
            'multifunction' => 'fas fa-copy',
            'label' => 'fas fa-tag',
            default => 'fas fa-print',
        };
    }

    /**
     * Scope for available printers
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for default printer
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}