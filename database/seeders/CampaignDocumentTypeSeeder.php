<?php

namespace Database\Seeders;

use App\Models\CampaignDocumentType;
use Illuminate\Database\Seeder;

class CampaignDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CampaignDocumentType::query()->updateOrCreate(
            ['name' => 'Company Profile'],
            [
                'description' => 'Upload your company profile or organizational profile document.',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
        );
    }
}
