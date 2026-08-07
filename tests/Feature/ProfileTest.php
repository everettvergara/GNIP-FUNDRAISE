<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'about_me' => 'I fundraise for good causes.',
                'organization' => 'Good Neighbors',
                'position' => 'Volunteer',
                'is_profile_public' => true,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test', $user->first_name);
        $this->assertSame('User', $user->last_name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('I fundraise for good causes.', $user->about_me);
        $this->assertSame('Good Neighbors', $user->organization);
        $this->assertSame('Volunteer', $user->position);
        $this->assertTrue($user->is_profile_public);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'is_profile_public' => true,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_user_cannot_delete_account_with_active_campaigns(): void
    {
        $user = User::factory()->create();

        Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Active Campaign',
            'slug' => 'active-campaign',
            'description' => 'Description',
            'goal_amount' => 1000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_ACTIVE,
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_account_deletion_preserves_campaigns(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::query()->create([
            'user_id' => $user->id,
            'title' => 'Ended Campaign',
            'slug' => 'ended-campaign',
            'description' => 'Description',
            'goal_amount' => 1000,
            'raised_amount' => 0,
            'status' => Campaign::STATUS_ENDED,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
        $this->assertNotNull($campaign->fresh());
        $this->assertNull($campaign->fresh()->user_id);
    }

    public function test_public_profile_is_visible_when_profile_is_public(): void
    {
        $user = User::factory()->create([
            'is_profile_public' => true,
            'about_me' => 'Public bio',
        ]);

        $this->get(route('fundraisers.show', $user))
            ->assertOk()
            ->assertSee('Public bio');
    }

    public function test_public_profile_is_hidden_when_profile_is_private(): void
    {
        $user = User::factory()->create([
            'is_profile_public' => false,
        ]);

        $this->get(route('fundraisers.show', $user))
            ->assertNotFound();
    }

    public function test_suspended_campaign_user_has_no_public_profile(): void
    {
        $user = User::factory()->create([
            'is_profile_public' => true,
            'is_active' => false,
        ]);

        $this->get(route('fundraisers.show', $user))
            ->assertNotFound();
    }

    public function test_suspended_user_cannot_enable_public_profile(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
            'is_profile_public' => false,
        ]);

        $this
            ->actingAs($user)
            ->patch('/profile', [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'is_profile_public' => true,
            ]);

        $this->assertFalse($user->fresh()->is_profile_public);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
