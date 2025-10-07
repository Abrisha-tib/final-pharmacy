<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'quantity_transferred',
        'quantity_remaining',
        'batch_number',
        'expiry_date',
        'notes',
        'status',
        'transfer_type',
        'transferred_by',
        'transferred_at'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'transferred_at' => 'datetime',
        'quantity_transferred' => 'integer',
        'quantity_remaining' => 'integer'
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInventoryToDispensary($query)
    {
        return $query->where('transfer_type', 'inventory_to_dispensary');
    }

    public function scopeDispensaryToInventory($query)
    {
        return $query->where('transfer_type', 'dispensary_to_inventory');
    }
}
