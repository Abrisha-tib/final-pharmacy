<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'medicine_id',
        'quantity',
        'unit_price',
        'total_price',
        'batch_number',
        'expiry_date'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'expiry_date' => 'date'
    ];

    /**
     * Get the sale that owns this item
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the medicine for this item
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute()
    {
        return 'Br ' . number_format($this->unit_price, 2);
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'Br ' . number_format($this->total_price, 2);
    }

    /**
     * Check if item is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Check if item is expiring soon (within 30 days)
     */
    public function getIsExpiringSoonAttribute()
    {
        return $this->expiry_date && $this->days_until_expiry <= 30 && $this->days_until_expiry > 0;
    }

    /**
     * Calculate total price based on quantity and unit price
     */
    public function calculateTotalPrice()
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Update total price when quantity or unit price changes
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($saleItem) {
            $saleItem->total_price = $saleItem->calculateTotalPrice();
        });
    }
}
