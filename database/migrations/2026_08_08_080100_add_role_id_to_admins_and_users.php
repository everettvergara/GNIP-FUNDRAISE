<?php

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
        });

        (new RoleSeeder)->run();

        $superAdminRole = Role::query()
            ->where('audience', Role::AUDIENCE_ADMIN)
            ->where('slug', Role::SLUG_SUPER_ADMIN)
            ->first();

        $supportRole = Role::query()
            ->where('audience', Role::AUDIENCE_ADMIN)
            ->where('slug', Role::SLUG_SUPPORT)
            ->first();

        $fundraiserRole = Role::query()
            ->where('audience', Role::AUDIENCE_CAMPAIGN_USER)
            ->where('slug', Role::SLUG_FUNDRAISER)
            ->first();

        if ($superAdminRole) {
            Admin::query()
                ->where('role', Role::SLUG_SUPER_ADMIN)
                ->update(['role_id' => $superAdminRole->id]);

            Admin::query()
                ->whereNull('role_id')
                ->update(['role_id' => $supportRole?->id ?? $superAdminRole->id]);
        }

        if ($fundraiserRole) {
            User::query()
                ->whereNull('role_id')
                ->update(['role_id' => $fundraiserRole->id]);
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('role')->default('support')->after('password');
        });

        $superAdminRole = Role::query()
            ->where('audience', Role::AUDIENCE_ADMIN)
            ->where('slug', Role::SLUG_SUPER_ADMIN)
            ->first();

        if ($superAdminRole) {
            Admin::query()
                ->where('role_id', $superAdminRole->id)
                ->update(['role' => Role::SLUG_SUPER_ADMIN]);

            Admin::query()
                ->whereNull('role')
                ->update(['role' => Role::SLUG_SUPPORT]);
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
