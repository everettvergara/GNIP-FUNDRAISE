<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use App\Policies\CampaignPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_any_campaign(): void
    {
        $admin = $this->createSuperAdmin([
            'email' => 'admin@example.com',
        ]);

        $owner = User::factory()->create();
        $campaign = Campaign::query()->create([
            'user_id' => $owner->id,
            'title' => 'Pending Campaign',
            'slug' => 'pending-campaign',
            'description' => 'Description',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_PENDING,
        ]);

        $policy = new CampaignPolicy;

        $this->assertTrue($policy->view($admin, $campaign));
        $this->assertTrue($policy->update($admin, $campaign));
        $this->assertTrue($policy->delete($admin, $campaign));
    }

    public function test_owner_can_only_view_own_campaign(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $campaign = Campaign::query()->create([
            'user_id' => $owner->id,
            'title' => 'Owner Campaign',
            'slug' => 'owner-campaign',
            'description' => 'Description',
            'goal_amount' => 5000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_DRAFT,
        ]);

        $policy = new CampaignPolicy;

        $this->assertTrue($policy->view($owner, $campaign));
        $this->assertFalse($policy->view($otherUser, $campaign));
    }
}
