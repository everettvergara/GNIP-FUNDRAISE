<?php

namespace App\Models;

use App\Support\ModuleAccess;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'avatar',
        'contact',
        'about_me',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function canAccessModule(string $module): bool
    {
        return ModuleAccess::can($this, $module);
    }

    public function isSuperAdmin(): bool
    {
        return ModuleAccess::isSuperAdmin($this);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->is_active;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        return asset('storage/'.$this->avatar);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function reviewedMatches(): HasMany
    {
        return $this->hasMany(DonorSponsorMatch::class, 'reviewed_by');
    }

    public function paymentReleases(): HasMany
    {
        return $this->hasMany(PaymentRelease::class, 'released_by');
    }
}
