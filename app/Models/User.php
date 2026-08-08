<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use App\Support\ModuleAccess;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'avatar',
        'about_me',
        'organization',
        'position',
        'is_profile_public',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_profile_public' => 'boolean',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim("{$this->first_name} {$this->last_name}"));
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->avatar) {
                return null;
            }

            return asset('storage/'.$this->avatar);
        });
    }

    protected function initials(): Attribute
    {
        return Attribute::get(function (): string {
            $first = mb_substr($this->first_name, 0, 1);
            $last = mb_substr($this->last_name, 0, 1);

            return mb_strtoupper(trim("{$first}{$last}"));
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function canAccessModule(string $module): bool
    {
        return ModuleAccess::can($this, $module);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function hasActiveCampaigns(): bool
    {
        return $this->campaigns()
            ->where('status', Campaign::STATUS_ACTIVE)
            ->exists();
    }

    public function isCampaignUser(): bool
    {
        return (bool) ($this->is_active ?? true);
    }

    /**
     * Campaign portal accounts (users table) are never Filament admins,
     * regardless of role slug (fundraiser, campaign_viewer, etc.).
     */
    public function isCampaignPortalAccount(): bool
    {
        $this->loadMissing('role');

        if ($this->role) {
            return $this->role->audience === Role::AUDIENCE_CAMPAIGN_USER;
        }

        return true;
    }

    public function hasPublicProfile(): bool
    {
        return $this->isCampaignUser() && $this->is_profile_public;
    }

    public function deleteAvatarFile(): void
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }
}
