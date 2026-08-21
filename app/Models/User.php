<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'status',
        'avatar_url',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = [
        'name',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''))
        );
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_user_id');
    }

    public function assignedDeals(): HasMany
    {
        return $this->hasMany(Deal::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'user_id');
    }
}
