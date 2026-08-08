<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Support\ModuleAccess;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminModules = ModuleAccess::allModuleKeysForAudience(Role::AUDIENCE_ADMIN);
        $campaignUserModules = ModuleAccess::allModuleKeysForAudience(Role::AUDIENCE_CAMPAIGN_USER);

        $this->seedRole(
            name: 'Super Admin',
            slug: Role::SLUG_SUPER_ADMIN,
            audience: Role::AUDIENCE_ADMIN,
            modules: $adminModules,
            description: 'Full access to all admin modules.',
            isSystem: true,
        );

        $this->seedRole(
            name: 'Campaign Reviewer',
            slug: 'campaign_reviewer',
            audience: Role::AUDIENCE_ADMIN,
            modules: ['dashboard', 'campaigns', 'campaign_categories', 'campaign_document_types'],
            description: 'Review and manage campaign submissions.',
        );

        $this->seedRole(
            name: 'Finance',
            slug: 'finance',
            audience: Role::AUDIENCE_ADMIN,
            modules: ['donations', 'payment_releases'],
            description: 'Manage donations and payment releases.',
        );

        $this->seedRole(
            name: 'Support',
            slug: Role::SLUG_SUPPORT,
            audience: Role::AUDIENCE_ADMIN,
            modules: ['campaign_users', 'donations', 'campaigns'],
            description: 'Support campaign users and view donations.',
            isSystem: true,
        );

        $this->seedRole(
            name: 'Content Editor',
            slug: 'content_editor',
            audience: Role::AUDIENCE_ADMIN,
            modules: ['cms_pages', 'partners', 'sectors'],
            description: 'Manage public site content.',
        );

        $this->seedRole(
            name: 'Fundraiser',
            slug: Role::SLUG_FUNDRAISER,
            audience: Role::AUDIENCE_CAMPAIGN_USER,
            modules: $campaignUserModules,
            description: 'Default role for registered campaign users.',
            isSystem: true,
        );

        $this->seedRole(
            name: 'Campaign Viewer',
            slug: Role::SLUG_CAMPAIGN_VIEWER,
            audience: Role::AUDIENCE_CAMPAIGN_USER,
            modules: ['dashboard', 'my_campaigns', 'donations', 'profile'],
            description: 'View campaigns and donations without creating or editing.',
            isSystem: true,
        );
    }

    /**
     * @param  list<string>  $modules
     */
    private function seedRole(
        string $name,
        string $slug,
        string $audience,
        array $modules,
        ?string $description = null,
        bool $isSystem = false,
    ): void {
        Role::query()->updateOrCreate(
            [
                'slug' => $slug,
                'audience' => $audience,
            ],
            [
                'name' => $name,
                'modules' => $modules,
                'description' => $description,
                'is_system' => $isSystem,
            ],
        );
    }
}
