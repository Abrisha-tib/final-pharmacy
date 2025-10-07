<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'metadata',
        'performed_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user that this activity belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who performed this activity.
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get the activity icon based on action.
     */
    public function getIconAttribute()
    {
        return match($this->action) {
            'user_created' => 'fas fa-user-plus',
            'user_updated' => 'fas fa-user-edit',
            'user_deleted' => 'fas fa-user-times',
            'password_reset' => 'fas fa-key',
            'account_locked' => 'fas fa-lock',
            'account_unlocked' => 'fas fa-unlock',
            'role_assigned' => 'fas fa-user-tag',
            'permission_granted' => 'fas fa-shield-alt',
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            default => 'fas fa-info-circle'
        };
    }

    /**
     * Get the activity color based on action.
     */
    public function getColorAttribute()
    {
        return match($this->action) {
            'user_created' => 'text-green-600',
            'user_updated' => 'text-blue-600',
            'user_deleted' => 'text-red-600',
            'password_reset' => 'text-yellow-600',
            'account_locked' => 'text-red-600',
            'account_unlocked' => 'text-green-600',
            'role_assigned' => 'text-purple-600',
            'permission_granted' => 'text-indigo-600',
            'login' => 'text-green-600',
            'logout' => 'text-gray-600',
            default => 'text-gray-600'
        };
    }
}
