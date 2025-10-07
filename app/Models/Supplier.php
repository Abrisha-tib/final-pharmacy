<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'location',
        'status',
        'rating',
        'total_orders',
        'on_time_delivery',
        'total_spent',
        'categories'
    ];

    protected $casts = [
        'categories' => 'array',
        'rating' => 'decimal:1',
        'total_spent' => 'decimal:2'
    ];
}
