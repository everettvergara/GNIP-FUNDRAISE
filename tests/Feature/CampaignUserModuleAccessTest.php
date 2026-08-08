<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Policies\CampaignPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignUserModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_fundraiser_can_create_campaigns(): void
    {
        $user = User::factory()->create();

        $policy = new CampaignPolicy;

        $this->assertTrue($policy->create($user));
        $this->actingAs($user)
            ->get(route('campaigns.create'))
            ->assertOk();
    }

    public function test_campaign_viewer_cannot_create_campaigns(): void
    {
        $viewerRole = Role::query()
            ->where('audience', Role::AUDIENCE_CAMPAIGN_USER)
            ->where('slug', Role::SLUG_CAMPAIGN_VIEWER)
            ->firstOrFail();

        $user = User::factory()->create([
            'role_id' => $viewerRole->id,
        ]);

        $policy = new CampaignPolicy;

        $this->assertFalse($policy->create($user));
        $this->actingAs($user)
            ->get(route('campaigns.create'))
            ->assertForbidden();
    }

    public function test_campaign_viewer_subnav_hides_create_link(): void
    {
        $viewerRole = Role::query()
            ->where('audience', Role::AUDIENCE_CAMPAIGN_USER)
            ->where('slug', Role::SLUG_CAMPAIGN_VIEWER)
            ->firstOrFail();

        $user = User::factory()->create([
            'role_id' => $viewerRole->id,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee(route('campaigns.create'), false);
    }

    public function test_fundraiser_subnav_shows_create_link(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(route('campaigns.create'), false);
    }
}
