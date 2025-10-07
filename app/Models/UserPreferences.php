<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreferences extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'language',
        'timezone',
        'date_format',
        'time_format',
        'currency',
        'currency_symbol',
        'notifications',
        'dashboard_widgets',
    ];

    protected $casts = [
        'notifications' => 'array',
        'dashboard_widgets' => 'array',
    ];

    /**
     * Get the user that owns the preferences.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}