<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use App\Services\CampaignReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignPublicVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_campaign_as_draft(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('campaigns.store'), [
            'title' => 'New Draft Campaign',
            'description' => 'Campaign description text.',
            'goal_amount' => 5000,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('campaigns', [
            'title' => 'New Draft Campaign',
            'status' => Campaign::STATUS_DRAFT,
            'user_id' => $user->id,
        ]);
    }

    public function test_browse_page_only_lists_admin_approved_active_campaigns(): void
    {
        $user = User::factory()->create();
        $admin = $this->createSuperAdmin([
            'email' => 'admin@example.com',
        ]);

        $approved = $this->createCampaign($user, 'Approved Listed Campaign', Campaign::STATUS_ACTIVE, [
            'submitted_at' => now()->subDay(),
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
        ]);

        $this->createCampaign($user, 'Unapproved Active Campaign', Campaign::STATUS_ACTIVE);
        $this->createCampaign($user, 'Draft Hidden Campaign', Campaign::STATUS_DRAFT);
        $this->createCampaign($user, 'Pending Hidden Campaign', Campaign::STATUS_PENDING);
        $this->createCampaign($user, 'Rejected Hidden Campaign', Campaign::STATUS_REJECTED);
        $this->createCampaign($user, 'Paused Hidden Campaign', Campaign::STATUS_PAUSED);
        $this->createCampaign($user, 'Ended Hidden Campaign', Campaign::STATUS_ENDED);

        $response = $this->get(route('campaigns.index'));

        $response
            ->assertOk()
            ->assertSee($approved->title)
            ->assertDontSee('Unapproved Active Campaign')
            ->assertDontSee('Draft Hidden Campaign')
            ->assertDontSee('Pending Hidden Campaign')
            ->assertDontSee('Rejected Hidden Campaign')
            ->assertDontSee('Paused Hidden Campaign')
            ->assertDontSee('Ended Hidden Campaign');
    }

    public function test_unapproved_active_campaign_is_not_publicly_accessible(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Fake Active Campaign', Campaign::STATUS_ACTIVE);

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertNotFound();
    }

    public function test_pending_campaign_is_not_accessible_on_public_show_page_for_guest(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Pending Public Campaign', Campaign::STATUS_PENDING);

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertNotFound();
    }

    public function test_pending_campaign_is_not_accessible_on_public_show_page_for_owner(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Pending Owner Campaign', Campaign::STATUS_PENDING);

        $this->actingAs($user)
            ->get(route('campaigns.show', $campaign->slug))
            ->assertNotFound();
    }

    public function test_draft_campaign_is_not_accessible_on_public_show_page_for_owner(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Draft Owner Campaign', Campaign::STATUS_DRAFT);

        $this->actingAs($user)
            ->get(route('campaigns.show', $campaign->slug))
            ->assertNotFound();
    }

    public function test_active_campaign_is_accessible_on_public_show_page(): void
    {
        $user = User::factory()->create();
        $admin = $this->createSuperAdmin([
            'email' => 'admin2@example.com',
        ]);
        $campaign = $this->createCampaign($user, 'Active Public Campaign', Campaign::STATUS_ACTIVE, [
            'submitted_at' => now()->subDay(),
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
        ]);

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertOk()
            ->assertSee('Active Public Campaign');
    }

    public function test_approved_campaign_appears_on_browse_and_show_pages(): void
    {
        $user = User::factory()->create();
        $admin = $this->createSuperAdmin([
            'email' => 'admin@example.com',
        ]);
        $campaign = $this->createCampaign($user, 'Awaiting Approval Campaign', Campaign::STATUS_PENDING);

        app(CampaignReviewService::class)->approve($campaign, $admin);

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_ACTIVE, $campaign->status);

        $this->get(route('campaigns.index'))
            ->assertOk()
            ->assertSee('Awaiting Approval Campaign');

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertOk()
            ->assertSee('Awaiting Approval Campaign');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createCampaign(User $user, string $title, string $status, array $attributes = []): Campaign
    {
        return Campaign::query()->create([
            'user_id' => $user->id,
            'title' => $title,
            'slug' => str($title)->slug()->toString(),
            'description' => 'Description for '.$title,
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => $status,
            ...$attributes,
        ]);
    }
}
