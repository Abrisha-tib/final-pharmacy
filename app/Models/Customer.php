<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'age',
        'loyalty_points',
        'total_spent',
        'status',
        'segment',
        'notes',
        'date_of_birth',
        'gender',
        'emergency_contact',
        'emergency_phone',
        'medical_conditions',
        'allergies',
        'insurance_provider',
        'insurance_number',
        'tags',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'loyalty_points' => 'integer',
        'total_spent' => 'decimal:2',
        'age' => 'integer'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'date_of_birth'
    ];

    /**
     * Get the customer's full address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Get the customer's display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: $this->email;
    }

    /**
     * Get the customer's status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'new' => 'green',
            'active' => 'blue',
            'premium' => 'purple',
            'inactive' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get the customer's segment color
     */
    public function getSegmentColorAttribute()
    {
        return match($this->segment) {
            'vip' => 'purple',
            'regular' => 'blue',
            'new' => 'green',
            'loyal' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Scope for active customers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for premium customers
     */
    public function scopePremium($query)
    {
        return $query->where('segment', 'vip');
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Get customer's sales by matching email
     */
    public function sales()
    {
        return \App\Models\Sale::where('customer_email', $this->email);
    }

    /**
     * Get customer's total sales count
     */
    public function getTotalSalesAttribute()
    {
        $count = $this->sales()->count();
        \Log::info("Customer {$this->id} ({$this->email}) has {$count} sales");
        return $count;
    }

    /**
     * Get customer's average order value
     */
    public function getAverageOrderValueAttribute()
    {
        $totalSales = $this->sales()->count();
        if ($totalSales === 0) return 0;
        
        return $this->total_spent / $totalSales;
    }
}
