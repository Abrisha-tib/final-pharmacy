<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'department',
        'notes',
        'avatar',
        'status',
        'last_login_at',
        'last_activity_at',
        'login_attempts',
        'locked_until',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Get the user who created this user.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this user.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=059669&background=f0fdf4';
    }

    /**
     * Check if user is locked.
     */
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Check if user is active.
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->isLocked();
    }

    /**
     * Get the user's activities.
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get the user's preferences.
     */
    public function preferences()
    {
        return $this->hasOne(UserPreferences::class);
    }

    /**
     * Update the user's last login time.
     */
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->last_activity_at = now();
        $this->login_attempts = 0; // Reset login attempts
        $this->save();
    }

    /**
     * Update the user's last activity time.
     */
    public function updateLastActivity()
    {
        $this->last_activity_at = now();
        $this->save();
    }
}
