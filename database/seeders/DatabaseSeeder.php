<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            CampaignDocumentTypeSeeder::class,
            CampaignCategorySeeder::class,
            SectorSeeder::class,
            CmsPageSeeder::class,
            PartnerSeeder::class,
            EmailTemplateSeeder::class,
            SiteSettingSeeder::class,
            CampaignSeeder::class,
        ]);
    }
}
