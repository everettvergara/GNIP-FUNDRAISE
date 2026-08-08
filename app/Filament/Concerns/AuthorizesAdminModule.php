<?php

namespace App\Filament\Concerns;

use App\Models\Admin;
use Filament\Facades\Filament;

trait AuthorizesAdminModule
{
    protected static function moduleKey(): string
    {
        throw new \LogicException('Define moduleKey() on '.static::class);
    }

    public static function canViewAny(): bool
    {
        $admin = Filament::auth()->user();

        return $admin instanceof Admin && $admin->canAccessModule(static::moduleKey());
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }
}
