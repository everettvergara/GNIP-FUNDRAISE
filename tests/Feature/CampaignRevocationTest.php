<?php

namespace Tests\Feature;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use App\Models\CampaignEvent;
use App\Models\User;
use App\Services\CampaignReviewService;
use App\Services\EmailTemplateMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CampaignRevocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_or_edit_campaigns_in_filament(): void
    {
        $admin = $this->createSuperAdmin([
            'name' => 'Panel Admin',
            'email' => 'panel@example.com',
        ]);
        $campaign = $this->createActiveCampaign();

        $this->assertFalse(CampaignResource::canCreate());
        $this->assertFalse(CampaignResource::canEdit($campaign));
        $this->assertFalse(CampaignResource::canDelete($campaign));
        $this->assertFalse(CampaignResource::canDeleteAny());

        $this->actingAs($admin, 'admin')
            ->get(CampaignResource::getUrl('index'))
            ->assertOk();

        $this->actingAs($admin, 'admin')
            ->get(CampaignResource::getUrl('view', ['record' => $campaign]))
            ->assertOk();
    }

    public function test_revoking_active_campaign_requires_reason_and_unpublishes(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
        ]);

        $admin = $this->createSuperAdmin([
            'email' => 'admin@example.com',
        ]);

        $campaign = $this->createActiveCampaign($user);
        $reason = 'Authorization letter expired.';

        $this->mock(EmailTemplateMailer::class, function (MockInterface $mock) use ($user, $reason, $campaign): void {
            $mock->shouldReceive('send')
                ->once()
                ->with(
                    'campaign_revoked',
                    $user->email,
                    [
                        'name' => 'Jane Doe',
                        'campaign_title' => $campaign->title,
                        'revocation_reason' => $reason,
                        'edit_url' => route('campaigns.edit', $campaign->slug),
                    ],
                );
        });

        app(CampaignReviewService::class)->revoke($campaign, $admin, $reason);

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_PAUSED, $campaign->status);
        $this->assertSame($reason, $campaign->revocation_reason);
        $this->assertFalse($campaign->isPubliclyListed());
        $this->assertTrue($campaign->isRevoked());

        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $campaign->id,
            'type' => CampaignEvent::TYPE_REVOKED,
            'comment' => $reason,
            'admin_id' => $admin->id,
        ]);
    }

    public function test_owner_sees_revocation_reason_on_edit_page(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createActiveCampaign($user);
        $campaign->update([
            'status' => Campaign::STATUS_PAUSED,
            'revocation_reason' => 'Policy violation.',
        ]);

        $this->actingAs($user)
            ->get(route('campaigns.edit', $campaign->slug))
            ->assertOk()
            ->assertSee('This campaign was revoked')
            ->assertSee('Policy violation.');
    }

    private function createActiveCampaign(?User $user = null): Campaign
    {
        $user ??= User::factory()->create();

        $admin = $this->createSuperAdmin([
            'name' => 'Reviewer',
            'email' => 'reviewer@example.com',
        ]);

        return Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Active Campaign',
            'slug' => 'active-campaign',
            'description' => 'Description.',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_ACTIVE,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);
    }
}
