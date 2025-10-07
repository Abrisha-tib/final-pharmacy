<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'status',
        'prescription_required',
        'notes',
        'sold_by',
        'sale_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'prescription_required' => 'boolean',
        'sale_date' => 'datetime'
    ];

    /**
     * Get the user who made the sale
     */
    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    /**
     * Get the sale items
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the medicines through sale items
     */
    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'sale_items')
                    ->withPivot('quantity', 'unit_price', 'total_price', 'batch_number', 'expiry_date')
                    ->withTimestamps();
    }

    /**
     * Scope for completed sales
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending sales
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for sales by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    /**
     * Scope for sales by payment method
     */
    public function scopeByPaymentMethod($query, $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    /**
     * Scope for sales by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted sale number
     */
    public function getFormattedSaleNumberAttribute()
    {
        return 'SALE-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return 'Br ' . number_format($this->total_amount, 2);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get payment method badge class
     */
    public function getPaymentBadgeClassAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'bg-green-100 text-green-800',
            'card' => 'bg-blue-100 text-blue-800',
            'mobile_payment' => 'bg-purple-100 text-purple-800',
            'bank_transfer' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Calculate total items in sale
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if sale can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'completed']);
    }

    /**
     * Check if sale can be refunded
     */
    public function canBeRefunded()
    {
        return $this->status === 'completed';
    }

    /**
     * Generate unique sale number
     */
    public static function generateSaleNumber()
    {
        $lastSale = self::orderBy('id', 'desc')->first();
        $nextId = $lastSale ? $lastSale->id + 1 : 1;
        return 'SALE-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
