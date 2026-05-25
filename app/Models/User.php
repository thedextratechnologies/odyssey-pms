<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'employee_id', 'password',
        'role_id', 'state_id', 'district_id', 'city_id',
        'manager_id', 'date_of_joining', 'status', 'must_change_password',
        'failed_login_attempts', 'locked_until',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'   => 'datetime',
        'locked_until'        => 'datetime',
        'date_of_joining'     => 'date',
        'must_change_password' => 'boolean',
        'password'            => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'state_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'district_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'city_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Role Helpers ───────────────────────────────────────────
    public function isSuperAdmin(): bool    { return $this->role?->name === Role::SUPER_ADMIN; }
    public function isSalesDirector(): bool { return $this->role?->name === Role::SALES_DIRECTOR; }
    public function isZoneManager(): bool   { return $this->role?->name === Role::ZONE_MANAGER; }
    public function isBDM(): bool           { return $this->role?->name === Role::BDM; }
    public function isBDE(): bool           { return $this->role?->name === Role::BDE; }

    public function hasRoleLevel(int $level): bool
    {
        return ($this->role?->level ?? 0) >= $level;
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canApproveQuotations(): bool
    {
        return $this->hasRoleLevel(Role::LEVEL_BDM);
    }

    // ── Account Lock Helpers ───────────────────────────────────
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
        if ($this->failed_login_attempts >= 5) {
            $this->locked_until = now()->addMinutes(15);
            $this->save();
        }
    }

    public function resetFailedAttempts(): void
    {
        $this->update(['failed_login_attempts' => 0, 'locked_until' => null]);
    }

    // ── Territory Scope Helper ─────────────────────────────────
    public function getTerritoryLabel(): string
    {
        return match($this->role?->name) {
            Role::SUPER_ADMIN, Role::SALES_DIRECTOR => 'National',
            Role::ZONE_MANAGER   => $this->state?->name ?? '—',
            Role::BDM            => $this->district?->name ?? $this->state?->name ?? '—',
            Role::BDE            => $this->city?->name ?? $this->district?->name ?? '—',
            default              => '—',
        };
    }

    // ── Query Scopes ───────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, string $roleName)
    {
        return $query->whereHas('role', fn($q) => $q->where('name', $roleName));
    }

    /**
     * Returns users that the current user can see based on territory hierarchy.
     */
    public function scopeVisibleTo($query, User $viewer)
    {
        if ($viewer->isSuperAdmin() || $viewer->isSalesDirector()) {
            return $query; // see all
        }
        if ($viewer->isZoneManager()) {
            return $query->where('state_id', $viewer->state_id);
        }
        if ($viewer->isBDM()) {
            return $query->where('district_id', $viewer->district_id);
        }
        // BDE sees only themselves
        return $query->where('id', $viewer->id);
    }
}
