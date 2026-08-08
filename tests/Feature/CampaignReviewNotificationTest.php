<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use App\Services\CampaignReviewService;
use App\Services\EmailTemplateMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CampaignReviewNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_campaign_sends_email_to_owner(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
        ]);

        $admin = $this->createSuperAdmin([
            'email' => 'admin@example.com',
        ]);

        $campaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Approved Campaign',
            'slug' => 'approved-campaign',
            'description' => 'Ready to go live.',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $this->mock(EmailTemplateMailer::class, function (MockInterface $mock) use ($user, $campaign): void {
            $mock->shouldReceive('send')
                ->once()
                ->with(
                    'campaign_published',
                    $user->email,
                    [
                        'name' => 'Jane Doe',
                        'campaign_title' => $campaign->title,
                        'campaign_url' => route('campaigns.show', $campaign->slug),
                    ],
                );
        });

        app(CampaignReviewService::class)->approve($campaign, $admin);

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_ACTIVE, $campaign->status);
    }

    public function test_rejecting_campaign_sends_email_with_reason_to_owner(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
        ]);

        $admin = $this->createSuperAdmin([
            'email' => 'admin@example.com',
        ]);

        $campaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Rejected Campaign',
            'slug' => 'rejected-campaign',
            'description' => 'Needs more detail.',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $reason = 'Please upload a clearer authorization letter.';

        $this->mock(EmailTemplateMailer::class, function (MockInterface $mock) use ($user, $reason, $campaign): void {
            $mock->shouldReceive('send')
                ->once()
                ->with(
                    'campaign_rejected',
                    $user->email,
                    [
                        'name' => 'Jane Doe',
                        'campaign_title' => $campaign->title,
                        'rejection_reason' => $reason,
                        'edit_url' => route('campaigns.edit', $campaign->slug),
                    ],
                );
        });

        app(CampaignReviewService::class)->reject($campaign, $admin, $reason);

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_REJECTED, $campaign->status);
        $this->assertSame($reason, $campaign->rejection_reason);
    }

    public function test_revoking_campaign_sends_email_with_reason_to_owner(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
        ]);

        $admin = $this->createSuperAdmin([
            'email' => 'admin@example.com',
        ]);

        $campaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Revoked Campaign',
            'slug' => 'revoked-campaign',
            'description' => 'Active campaign.',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_ACTIVE,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

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
    }
}
