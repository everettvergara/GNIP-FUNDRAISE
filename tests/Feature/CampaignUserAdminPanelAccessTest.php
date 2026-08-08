<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignUserAdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_campaign_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Campaign accounts cannot access the admin panel.');
    }

    public function test_authenticated_campaign_user_cannot_access_admin_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/login')
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Campaign accounts cannot access the admin panel.');
    }

    public function test_guest_can_view_admin_login(): void
    {
        $this->get('/admin/login')
            ->assertOk();
    }

    public function test_authenticated_campaign_viewer_role_cannot_access_admin_panel(): void
    {
        $viewerRole = Role::query()
            ->where('audience', Role::AUDIENCE_CAMPAIGN_USER)
            ->where('slug', Role::SLUG_CAMPAIGN_VIEWER)
            ->firstOrFail();

        $user = User::factory()->create([
            'role_id' => $viewerRole->id,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = Admin::query()->create([
            'name' => 'Panel Admin',
            'email' => 'panel@example.com',
            'password' => 'password',
            'role_id' => Role::superAdmin()->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk();
    }
}
