<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice', absolute: false));

        $user = User::query()->where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(Role::SLUG_FUNDRAISER, $user->role?->slug);
    }

    public function test_registration_ignores_role_id_spoof(): void
    {
        $superAdminRole = Role::superAdmin();

        $this->post('/register', [
            'first_name' => 'Spoof',
            'last_name' => 'User',
            'email' => 'spoof@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $superAdminRole->id,
        ]);

        $user = User::query()->where('email', 'spoof@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(Role::SLUG_FUNDRAISER, $user->role?->slug);
        $this->assertNotSame($superAdminRole->id, $user->role_id);
    }
}
