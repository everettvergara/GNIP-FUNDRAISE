<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectors = [
            [
                'name' => 'Child Protection',
                'slug' => 'child-protection',
                'category' => Sector::CATEGORY_QUALITY_OF_LIFE,
                'description' => 'Good Neighbors advocates for children\'s safety and well-being in every community we serve. We work with families, schools, and local partners to prevent abuse, neglect, and exploitation. Through awareness programs and protective services, we help create environments where children can grow with dignity. Every child deserves a safe childhood, and we stand with those who need it most.',
                'image' => 'images/sectors/child-protection.jpg',
                'sort_order' => 1,
            ],
            [
                'name' => 'Disaster Risk Reduction',
                'slug' => 'disaster-risk-reduction',
                'category' => Sector::CATEGORY_QUALITY_OF_LIFE,
                'description' => 'Good Neighbors builds international cooperation and emergency relief systems so communities can respond the moment disaster strikes. We focus on countries where hazards occur frequently, minimizing damage through rapid, coordinated action. Beyond immediate relief, we help families and local leaders recover livelihoods and restore essential services. Preparedness and resilience remain at the heart of every response.',
                'image' => 'images/sectors/disaster-risk-reduction.jpg',
                'sort_order' => 2,
            ],
            [
                'name' => 'Economic Empowerment',
                'slug' => 'economic-empowerment',
                'category' => Sector::CATEGORY_QUALITY_OF_LIFE,
                'description' => 'Limited access to economic and financial resources remains a major driver of poverty in the areas where Good Neighbors works. We support livelihood training, savings groups, and small enterprise opportunities that put income within reach of families. Women and youth are prioritized so households can build lasting financial resilience. Empowerment means choice, stability, and a path out of economic powerlessness.',
                'image' => 'images/sectors/economic-empowerment.png',
                'sort_order' => 3,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'category' => Sector::CATEGORY_REDUCE_INEQUALITY,
                'description' => 'At Good Neighbors, we focus on ensuring every child receives a quality education. We improve teaching and learning by training teachers and strengthening school communities. Learning materials, safe classrooms, and inclusive programs help children stay in school and succeed. Education remains one of the strongest tools for breaking cycles of inequality.',
                'image' => 'images/sectors/education.png',
                'sort_order' => 1,
            ],
            [
                'name' => 'Health',
                'slug' => 'health',
                'category' => Sector::CATEGORY_REDUCE_INEQUALITY,
                'description' => 'We are committed to improving children\'s health through holistic community programs. From nutrition and maternal care to disease prevention and medical outreach, our work reaches families where services are scarce. Partnering with local health workers, we promote lasting habits that keep children well. Healthy children learn better, play more, and grow into stronger communities.',
                'image' => 'images/sectors/health.jpg',
                'sort_order' => 2,
            ],
            [
                'name' => 'Sustainable Environment',
                'slug' => 'sustainable-environment',
                'category' => Sector::CATEGORY_REDUCE_INEQUALITY,
                'description' => 'Good Neighbors is an international humanitarian organization dedicated to improving the lives of children and communities in over 50 countries. We promote environmental stewardship so families can thrive on land and resources that last. Clean water, climate-aware farming, and community conservation projects sit at the center of this work. A sustainable environment today protects the next generation tomorrow.',
                'image' => 'images/sectors/sustainable-environment.png',
                'sort_order' => 3,
            ],
        ];

        foreach ($sectors as $sector) {
            Sector::query()->updateOrCreate(
                ['slug' => $sector['slug']],
                $sector,
            );
        }
    }
}
