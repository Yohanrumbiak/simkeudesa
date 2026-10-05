<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'nip',
        'email',
        'password',
        'role',
        'village_id',
        'position',
        'phone',
        'avatar',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVillageHead(): bool
    {
        return $this->role === 'kepala_desa';
    }

    public function isLeader(): bool
    {
        return $this->role === 'pimpinan';
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'bg-blue-100 text-blue-800 border-blue-200',
            'kepala_desa' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'pimpinan' => 'bg-purple-100 text-purple-800 border-purple-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Staff Pemda / Administrator',
            'kepala_desa' => 'Kepala Desa',
            'pimpinan' => 'Pimpinan Pemda',
            default => 'Pengguna',
        };
    }

    public function getIdentifierAttribute(): string
    {
        return $this->nip ?: ($this->username ?: $this->email);
    }
}
