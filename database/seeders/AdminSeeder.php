<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::query()->updateOrCreate(
            ['email' => 'admin@goodneighbors.ph'],
            [
                'name' => 'GNIP Administrator',
                'password' => 'password',
                'role_id' => Role::superAdmin()->id,
                'is_active' => true,
            ],
        );
    }
}
