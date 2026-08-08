<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const AUDIENCE_ADMIN = 'admin';

    public const AUDIENCE_CAMPAIGN_USER = 'campaign_user';

    public const SLUG_SUPER_ADMIN = 'super_admin';

    public const SLUG_SUPPORT = 'support';

    public const SLUG_FUNDRAISER = 'fundraiser';

    public const SLUG_CAMPAIGN_VIEWER = 'campaign_viewer';

    protected $fillable = [
        'name',
        'slug',
        'audience',
        'modules',
        'description',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'modules' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @param  Builder<Role>  $query
     */
    public function scopeForAdmin(Builder $query): Builder
    {
        return $query->where('audience', self::AUDIENCE_ADMIN);
    }

    /**
     * @param  Builder<Role>  $query
     */
    public function scopeForCampaignUser(Builder $query): Builder
    {
        return $query->where('audience', self::AUDIENCE_CAMPAIGN_USER);
    }

    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules ?? [], true);
    }

    public static function defaultCampaignUser(): Role
    {
        return static::query()
            ->where('audience', self::AUDIENCE_CAMPAIGN_USER)
            ->where('slug', self::SLUG_FUNDRAISER)
            ->firstOrFail();
    }

    public static function defaultAdmin(): Role
    {
        return static::query()
            ->where('audience', self::AUDIENCE_ADMIN)
            ->where('slug', self::SLUG_SUPPORT)
            ->firstOrFail();
    }

    public static function superAdmin(): Role
    {
        return static::query()
            ->where('audience', self::AUDIENCE_ADMIN)
            ->where('slug', self::SLUG_SUPER_ADMIN)
            ->firstOrFail();
    }
}
