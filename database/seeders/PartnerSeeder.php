<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            ['name' => 'Good Neighbors International', 'url' => 'https://www.goodneighbors.org', 'sort_order' => 1],
            ['name' => 'Local Government Partner', 'url' => null, 'sort_order' => 2],
            ['name' => 'Corporate Supporter', 'url' => null, 'sort_order' => 3],
            ['name' => 'Community Foundation', 'url' => null, 'sort_order' => 4],
        ];

        foreach ($partners as $partner) {
            Partner::query()->updateOrCreate(
                ['name' => $partner['name']],
                $partner,
            );
        }
    }
}
