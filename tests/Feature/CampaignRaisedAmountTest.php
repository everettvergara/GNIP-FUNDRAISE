<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignRaisedAmountTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmed_donation_updates_campaign_raised_amount(): void
    {
        $campaign = $this->createCampaign();

        Donation::query()->create([
            'campaign_id' => $campaign->id,
            'donor_name' => 'Test Donor',
            'donor_email' => 'donor@example.com',
            'amount' => 500,
            'type' => Donation::TYPE_ONE_TIME,
            'status' => Donation::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);

        $this->assertSame('500.00', $campaign->fresh()->raised_amount);
    }

    public function test_pending_donation_does_not_count_toward_raised_amount(): void
    {
        $campaign = $this->createCampaign();

        Donation::query()->create([
            'campaign_id' => $campaign->id,
            'donor_name' => 'Test Donor',
            'donor_email' => 'donor@example.com',
            'amount' => 500,
            'type' => Donation::TYPE_ONE_TIME,
            'status' => Donation::STATUS_PENDING,
        ]);

        $this->assertSame('0.00', $campaign->fresh()->raised_amount);
    }

    public function test_confirmed_donation_counts_toward_raised_amount_immediately_on_create(): void
    {
        $campaign = $this->createCampaign();

        Donation::query()->create([
            'campaign_id' => $campaign->id,
            'donor_name' => 'Test Donor',
            'donor_email' => 'donor@example.com',
            'amount' => 250,
            'type' => Donation::TYPE_ONE_TIME,
            'status' => Donation::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);

        $this->assertSame('250.00', $campaign->fresh()->raised_amount);
    }

    public function test_three_month_recurring_counts_full_commitment(): void
    {
        $campaign = $this->createCampaign();

        Donation::query()->create([
            'campaign_id' => $campaign->id,
            'donor_name' => 'Test Donor',
            'donor_email' => 'donor@example.com',
            'amount' => 1000,
            'type' => Donation::TYPE_MONTHLY_3,
            'status' => Donation::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);

        $this->assertSame('3000.00', $campaign->fresh()->raised_amount);
    }

    public function test_six_month_recurring_counts_full_commitment(): void
    {
        $campaign = $this->createCampaign();

        Donation::query()->create([
            'campaign_id' => $campaign->id,
            'donor_name' => 'Test Donor',
            'donor_email' => 'donor@example.com',
            'amount' => 500,
            'type' => Donation::TYPE_MONTHLY_6,
            'status' => Donation::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);

        $this->assertSame('3000.00', $campaign->fresh()->raised_amount);
    }

    public function test_cancelling_donation_removes_amount_from_raised_total(): void
    {
        $campaign = $this->createCampaign();

        $donation = Donation::query()->create([
            'campaign_id' => $campaign->id,
            'donor_name' => 'Test Donor',
            'donor_email' => 'donor@example.com',
            'amount' => 750,
            'type' => Donation::TYPE_ONE_TIME,
            'status' => Donation::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);

        $donation->update(['status' => Donation::STATUS_CANCELLED]);

        $this->assertSame('0.00', $campaign->fresh()->raised_amount);
    }

    private function createCampaign(): Campaign
    {
        $user = User::factory()->create();

        return Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Test Campaign',
            'slug' => 'test-campaign',
            'description' => 'Test description',
            'goal_amount' => 10000,
            'raised_amount' => 0,
            'status' => 'active',
        ]);
    }
}
