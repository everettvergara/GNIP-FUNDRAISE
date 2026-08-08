<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignDocumentType;
use App\Models\CampaignEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_withdraw_pending_submission_and_edit_again(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Withdraw Test Campaign', Campaign::STATUS_PENDING, [
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('campaigns.withdraw-submission', $campaign->slug))
            ->assertRedirect(route('campaigns.edit', $campaign->slug));

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_DRAFT, $campaign->status);
        $this->assertNull($campaign->submitted_at);

        $this->actingAs($user)
            ->get(route('campaigns.edit', $campaign->slug))
            ->assertOk()
            ->assertSee('Save as Draft')
            ->assertDontSee('Undo Submission');
    }

    public function test_pending_campaign_edit_page_is_read_only_with_undo_submission(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Read Only Pending Campaign', Campaign::STATUS_PENDING, [
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('campaigns.edit', $campaign->slug))
            ->assertOk()
            ->assertSee('Read Only Pending Campaign')
            ->assertSee('Undo Submission')
            ->assertDontSee('Save as Draft')
            ->assertDontSee('Save and Submit for Approval')
            ->assertDontSee('Choose a file')
            ->assertDontSee('Choose files');
    }

    public function test_owner_can_save_as_draft_without_submitting(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Draft Save Campaign', Campaign::STATUS_DRAFT);

        $this->actingAs($user)
            ->put(route('campaigns.update', $campaign->slug), [
                'title' => 'Draft Save Campaign',
                'description' => 'Updated description.',
                'goal_amount' => 7500,
            ])
            ->assertRedirect(route('campaigns.edit', $campaign->slug));

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_DRAFT, $campaign->status);
        $this->assertNull($campaign->submitted_at);
        $this->assertSame('Draft Save Campaign', $campaign->title);
    }

    public function test_owner_can_save_and_submit_for_approval_from_edit_page(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Submit Me', Campaign::STATUS_DRAFT);

        $this->actingAs($user)
            ->put(route('campaigns.update', $campaign->slug), [
                'title' => 'Submit Me',
                'description' => 'Updated description.',
                'goal_amount' => 6000,
                'submit_for_approval' => '1',
            ])
            ->assertRedirect(route('campaigns.edit', $campaign->slug));

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_PENDING, $campaign->status);
        $this->assertNotNull($campaign->submitted_at);
        $this->assertSame('Submit Me', $campaign->title);

        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $campaign->id,
            'type' => CampaignEvent::TYPE_SUBMITTED,
            'user_id' => $user->id,
        ]);
    }

    public function test_submission_comment_is_saved_with_submit_event(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Comment Test Campaign', Campaign::STATUS_DRAFT);

        $this->actingAs($user)
            ->put(route('campaigns.update', $campaign->slug), [
                'title' => 'Comment Test Campaign',
                'description' => 'Description.',
                'goal_amount' => 6000,
                'submit_for_approval' => '1',
                'submission_comment' => 'Please review my updated documents.',
            ])
            ->assertRedirect(route('campaigns.edit', $campaign->slug));

        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $campaign->id,
            'type' => CampaignEvent::TYPE_SUBMITTED,
            'comment' => 'Please review my updated documents.',
        ]);
    }

    public function test_withdrawal_creates_history_event(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Withdraw History Campaign', Campaign::STATUS_PENDING, [
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('campaigns.withdraw-submission', $campaign->slug));

        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $campaign->id,
            'type' => CampaignEvent::TYPE_WITHDRAWN,
            'user_id' => $user->id,
        ]);
    }

    public function test_edit_page_shows_campaign_history_sidebar(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'History Sidebar Campaign', Campaign::STATUS_DRAFT);

        $campaign->events()->create([
            'type' => CampaignEvent::TYPE_SUBMITTED,
            'comment' => 'First submission note.',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('campaigns.edit', $campaign->slug))
            ->assertOk()
            ->assertSee('Campaign History')
            ->assertSee('Submitted for review')
            ->assertSee('First submission note.');
    }

    public function test_save_and_submit_fails_when_required_documents_are_missing(): void
    {
        $user = User::factory()->create();
        $documentType = CampaignDocumentType::query()->create([
            'name' => 'Required ID',
            'description' => 'Government ID',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $campaign = $this->createCampaign($user, 'Incomplete Docs Campaign', Campaign::STATUS_DRAFT);

        $this->actingAs($user)
            ->put(route('campaigns.update', $campaign->slug), [
                'title' => 'Incomplete Docs Campaign',
                'description' => 'Description.',
                'goal_amount' => 5000,
                'submit_for_approval' => '1',
            ])
            ->assertRedirect(route('campaigns.edit', $campaign->slug))
            ->assertSessionHasErrors('submit_for_approval');

        $campaign->refresh();

        $this->assertSame(Campaign::STATUS_DRAFT, $campaign->status);
        $this->assertNull($campaign->submitted_at);
        $this->assertTrue($campaign->missingRequiredDocumentTypes()->pluck('id')->contains($documentType->id));
    }

    public function test_owner_cannot_update_campaign_while_pending(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createCampaign($user, 'Locked Pending Campaign', Campaign::STATUS_PENDING, [
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->put(route('campaigns.update', $campaign->slug), [
                'title' => 'Changed Title',
                'description' => 'Changed description.',
                'goal_amount' => 10000,
            ])
            ->assertForbidden();

        $campaign->refresh();

        $this->assertSame('Locked Pending Campaign', $campaign->title);
    }

    public function test_other_user_cannot_withdraw_pending_submission(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $campaign = $this->createCampaign($owner, 'Protected Pending Campaign', Campaign::STATUS_PENDING, [
            'submitted_at' => now(),
        ]);

        $this->actingAs($otherUser)
            ->post(route('campaigns.withdraw-submission', $campaign->slug))
            ->assertForbidden();
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
