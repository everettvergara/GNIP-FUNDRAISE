<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;

class ModuleAccess
{
    public static function can(Admin|User $actor, string $module): bool
    {
        if ($actor instanceof Admin && static::isSuperAdmin($actor)) {
            return true;
        }

        $actor->loadMissing('role');

        $role = $actor->role;

        if (! $role) {
            return false;
        }

        return $role->hasModule($module);
    }

    public static function isSuperAdmin(Admin $admin): bool
    {
        $admin->loadMissing('role');

        return $admin->role?->slug === Role::SLUG_SUPER_ADMIN
            && $admin->role?->audience === Role::AUDIENCE_ADMIN;
    }

    /**
     * @return array<string, string>
     */
    public static function allModuleKeysForAudience(string $audience): array
    {
        return array_keys(config("access_modules.{$audience}", []));
    }

    /**
     * @return array<string, string>
     */
    public static function optionsForAudience(?string $audience): array
    {
        if (! $audience) {
            return [];
        }

        $modules = config("access_modules.{$audience}", []);

        return collect($modules)
            ->mapWithKeys(fn (array $module, string $key): array => [$key => $module['label']])
            ->all();
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function groupedOptionsForAudience(?string $audience): array
    {
        if (! $audience) {
            return [];
        }

        $modules = config("access_modules.{$audience}", []);
        $grouped = [];

        foreach ($modules as $key => $module) {
            $group = $module['group'] ?? 'General';
            $grouped[$group][$key] = $module['label'];
        }

        return $grouped;
    }

    /**
     * @return list<string>
     */
    public static function modulesFor(Role $role): array
    {
        if ($role->audience === Role::AUDIENCE_ADMIN && $role->slug === Role::SLUG_SUPER_ADMIN) {
            return static::allModuleKeysForAudience(Role::AUDIENCE_ADMIN);
        }

        return $role->modules ?? [];
    }
}
