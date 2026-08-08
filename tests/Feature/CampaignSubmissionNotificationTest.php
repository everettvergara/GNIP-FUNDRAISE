<?php

namespace Tests\Feature;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use App\Models\CampaignEvent;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\EmailTemplateMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CampaignSubmissionNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_campaign_sends_email_to_site_notification_address(): void
    {
        SiteSetting::query()->create([
            'notification_email' => 'notifications@goodneighbors.ph',
        ]);

        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
        ]);

        $campaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'New Campaign',
            'slug' => 'new-campaign',
            'description' => 'Description.',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_DRAFT,
        ]);

        $this->mock(EmailTemplateMailer::class, function (MockInterface $mock) use ($campaign, $user): void {
            $mock->shouldReceive('send')
                ->once()
                ->withArgs(function (string $templateKey, string $to, array $placeholders) use ($campaign, $user): bool {
                    return $templateKey === 'campaign_submitted_admin'
                        && $to === 'notifications@goodneighbors.ph'
                        && $placeholders['campaign_title'] === $campaign->title
                        && $placeholders['fundraiser_name'] === 'Jane Doe'
                        && $placeholders['fundraiser_email'] === $user->email
                        && $placeholders['admin_review_url'] === CampaignResource::getUrl('view', ['record' => $campaign]);
                });
        });

        $this->actingAs($user)
            ->put(route('campaigns.update', $campaign->slug), [
                'title' => 'New Campaign',
                'description' => 'Description.',
                'goal_amount' => 5000,
                'submit_for_approval' => '1',
            ])
            ->assertRedirect(route('campaigns.edit', $campaign->slug));

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_PENDING, $campaign->status);
        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $campaign->id,
            'type' => CampaignEvent::TYPE_SUBMITTED,
            'user_id' => $user->id,
        ]);
    }

    public function test_submit_for_approval_route_also_notifies_admin(): void
    {
        SiteSetting::query()->create([
            'notification_email' => 'notifications@goodneighbors.ph',
        ]);

        $user = User::factory()->create();
        $campaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Route Submit Campaign',
            'slug' => 'route-submit-campaign',
            'description' => 'Description.',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_DRAFT,
        ]);

        $this->mock(EmailTemplateMailer::class, function (MockInterface $mock): void {
            $mock->shouldReceive('send')
                ->once()
                ->with(
                    'campaign_submitted_admin',
                    'notifications@goodneighbors.ph',
                    Mockery::type('array'),
                );
        });

        $this->actingAs($user)
            ->post(route('campaigns.submit-for-approval', $campaign->slug))
            ->assertRedirect(route('campaigns.edit', $campaign->slug));

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_PENDING, $campaign->status);
    }
}
