<?php

namespace App\Filament\Pages;

use App\Models\Admin;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        $admin = Filament::auth()->user();

        return $admin instanceof Admin && $admin->canAccessModule('dashboard');
    }
}
