<?php

namespace Database\Seeders;

use App\Models\CampaignCategory;
use Illuminate\Database\Seeder;

class CampaignCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Support learning programs for children and communities.', 'sort_order' => 1],
            ['name' => 'Health', 'slug' => 'health', 'description' => 'Fund health services, nutrition, and medical outreach.', 'sort_order' => 2],
            ['name' => 'Child Protection', 'slug' => 'child-protection', 'description' => 'Protect vulnerable children and strengthen families.', 'sort_order' => 3],
            ['name' => 'Disaster Risk Reduction', 'slug' => 'disaster-risk-reduction', 'description' => 'Help communities prepare for and recover from disasters.', 'sort_order' => 4],
            ['name' => 'Economic Empowerment', 'slug' => 'economic-empowerment', 'description' => 'Enable livelihoods and financial resilience.', 'sort_order' => 5],
            ['name' => 'Sustainable Environment', 'slug' => 'sustainable-environment', 'description' => 'Promote environmental stewardship and sustainability.', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            CampaignCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
