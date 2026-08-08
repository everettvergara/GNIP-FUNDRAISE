<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_all_admin_modules(): void
    {
        $admin = $this->createSuperAdmin();

        foreach (array_keys(config('access_modules.admin', [])) as $module) {
            $this->assertTrue($admin->canAccessModule($module), "Failed module: {$module}");
        }
    }

    public function test_support_admin_cannot_access_restricted_modules(): void
    {
        $supportRole = Role::query()
            ->where('audience', Role::AUDIENCE_ADMIN)
            ->where('slug', Role::SLUG_SUPPORT)
            ->firstOrFail();

        $admin = Admin::query()->create([
            'name' => 'Support Admin',
            'email' => 'support@example.com',
            'password' => 'password',
            'role_id' => $supportRole->id,
            'is_active' => true,
        ]);

        $this->assertTrue($admin->canAccessModule('campaign_users'));
        $this->assertFalse($admin->canAccessModule('settings'));
        $this->assertFalse($admin->canAccessModule('roles'));
        $this->assertFalse($admin->canAccessModule('admins'));
    }
}
