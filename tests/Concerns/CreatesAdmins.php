<?php

namespace Tests\Concerns;

use App\Models\Admin;
use App\Models\Role;

trait CreatesAdmins
{
    protected function createSuperAdmin(array $attributes = []): Admin
    {
        return Admin::query()->create(array_merge([
            'name' => 'Test Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role_id' => Role::superAdmin()->id,
            'is_active' => true,
        ], $attributes));
    }
}
