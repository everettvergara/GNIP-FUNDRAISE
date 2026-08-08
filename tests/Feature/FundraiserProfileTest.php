<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FundraiserProfileTest extends TestCase
{
    use RefreshDatabase;

    private function approvedCampaignAttributes(): array
    {
        $admin = $this->createSuperAdmin([
            'email' => 'admin@example.com',
        ]);

        return [
            'submitted_at' => now()->subDay(),
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
        ];
    }

    public function test_public_profile_shows_user_details_and_active_campaigns(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'about_me' => 'Passionate fundraiser.',
            'organization' => 'GNIP',
            'position' => 'Coordinator',
            'is_profile_public' => true,
        ]);

        $activeCampaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Active Fundraiser Campaign',
            'slug' => 'active-fundraiser-campaign',
            'description' => 'Active description',
            'goal_amount' => 5000,
            'raised_amount' => 100,
            'status' => Campaign::STATUS_ACTIVE,
            ...$this->approvedCampaignAttributes(),
        ]);

        Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Draft Campaign',
            'slug' => 'draft-campaign',
            'description' => 'Draft description',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_DRAFT,
        ]);

        $response = $this->get(route('fundraisers.show', $user));

        $response
            ->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee('Passionate fundraiser.')
            ->assertSee('Coordinator at GNIP')
            ->assertSee('Active Fundraiser Campaign')
            ->assertDontSee('Draft Campaign');
    }

    public function test_campaign_show_page_links_to_fundraiser_profile(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Link',
            'last_name' => 'Test',
            'is_profile_public' => true,
            'is_active' => true,
        ]);

        $campaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Linked Campaign',
            'slug' => 'linked-campaign',
            'description' => 'Description',
            'goal_amount' => 1000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_ACTIVE,
            ...$this->approvedCampaignAttributes(),
        ]);

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertOk()
            ->assertSee(route('fundraisers.show', $user), false)
            ->assertSee('Link Test');
    }

    public function test_campaign_show_page_does_not_link_suspended_campaign_user_profile(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Suspended',
            'last_name' => 'User',
            'is_profile_public' => true,
            'is_active' => false,
        ]);

        $campaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Suspended User Campaign',
            'slug' => 'suspended-user-campaign',
            'description' => 'Description',
            'goal_amount' => 1000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_ACTIVE,
            ...$this->approvedCampaignAttributes(),
        ]);

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertOk()
            ->assertDontSee(route('fundraisers.show', $user), false)
            ->assertSee('Suspended User');
    }
}
