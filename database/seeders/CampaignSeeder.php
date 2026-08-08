<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'fundraiser@example.com'],
            [
                'first_name' => 'Jayvee',
                'last_name' => 'Sabinay',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        $reviewedAt = now()->subWeek();
        $reviewedBy = Admin::query()->where('email', 'admin@goodneighbors.ph')->value('id');

        $categoryIds = CampaignCategory::query()
            ->whereIn('slug', [
                'education',
                'health',
                'child-protection',
                'disaster-risk-reduction',
                'economic-empowerment',
            ])
            ->pluck('id', 'slug');

        $campaigns = [
            [
                'user_id' => $user->id,
                'category_id' => $categoryIds['education'] ?? null,
                'title' => 'Books for Barangay Learners',
                'slug' => 'books-for-barangay-learners',
                'description' => 'Description text here. You can talk about the work your organization does, and why donations to your campaign are so important. This is the place where you can touch donors hearts and souls. You can upload videos and images as well.',
                'goal_amount' => 50000.00,
                'cover_image' => $this->seedCoverImage('books-for-barangay-learners.png'),
                'thank_you_message' => 'Thank you for giving us the opportunity to support this meaningful cause. We hope our small contribution helps make a positive impact in the lives of those who need it most. Wishing your organization continued success in all that you do.',
                'status' => 'active',
                'is_featured' => true,
                'starts_at' => now()->subMonth()->toDateString(),
                'ends_at' => now()->addMonths(2)->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categoryIds['health'] ?? null,
                'title' => 'Community Health Outreach',
                'slug' => 'community-health-outreach',
                'description' => 'Help us bring medical checkups, vitamins, and nutrition support to families in underserved communities across the Philippines.',
                'goal_amount' => 75000.00,
                'cover_image' => $this->seedCoverImage('community-health-outreach.jpg'),
                'thank_you_message' => 'Happy to support this meaningful cause. Keep up the great work!',
                'status' => 'active',
                'is_featured' => true,
                'starts_at' => now()->subWeeks(2)->toDateString(),
                'ends_at' => now()->addMonths(3)->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categoryIds['child-protection'] ?? null,
                'title' => 'Safe Spaces for Children',
                'slug' => 'safe-spaces-for-children',
                'description' => 'Create safe, supportive environments where vulnerable children can learn, play, and heal with access to counseling and protection services.',
                'goal_amount' => 100000.00,
                'cover_image' => $this->seedCoverImage('safe-spaces-for-children.jpg'),
                'thank_you_message' => 'Thank you for helping us protect and nurture children in our communities.',
                'status' => 'active',
                'is_featured' => true,
                'starts_at' => now()->subWeeks(3)->toDateString(),
                'ends_at' => now()->addMonths(4)->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categoryIds['disaster-risk-reduction'] ?? null,
                'title' => 'Typhoon Relief Fund',
                'slug' => 'typhoon-relief-fund',
                'description' => 'Provide emergency food packs, clean water, and shelter materials to families affected by recent typhoons in coastal communities.',
                'goal_amount' => 120000.00,
                'cover_image' => $this->seedCoverImage('typhoon-relief-fund.jpg'),
                'thank_you_message' => 'Your generosity brings hope to families rebuilding after disaster.',
                'status' => 'active',
                'is_featured' => true,
                'starts_at' => now()->subWeek()->toDateString(),
                'ends_at' => now()->addMonths(2)->toDateString(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $categoryIds['economic-empowerment'] ?? null,
                'title' => 'Livelihood Starter Kits',
                'slug' => 'livelihood-starter-kits',
                'description' => 'Equip parents and young adults with tools, training, and seed capital to start small businesses and achieve financial independence.',
                'goal_amount' => 60000.00,
                'cover_image' => $this->seedCoverImage('livelihood-starter-kits.png'),
                'thank_you_message' => 'Thank you for investing in sustainable livelihoods for Filipino families.',
                'status' => 'active',
                'is_featured' => false,
                'starts_at' => now()->subDays(10)->toDateString(),
                'ends_at' => now()->addMonths(5)->toDateString(),
            ],
        ];

        foreach ($campaigns as $campaignData) {
            if ($campaignData['status'] === Campaign::STATUS_ACTIVE) {
                $campaignData['submitted_at'] = $reviewedAt->copy()->subDay();
                $campaignData['reviewed_at'] = $reviewedAt;
                $campaignData['reviewed_by'] = $reviewedBy;
            }

            $campaign = Campaign::query()->updateOrCreate(
                ['slug' => $campaignData['slug']],
                $campaignData,
            );

            Donation::query()->updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'donor_email' => 'nicole.flores@example.com',
                    'amount' => 1000.00,
                ],
                [
                    'donor_name' => 'Nicole Flores',
                    'message' => 'Happy to support this meaningful cause!',
                    'show_name' => true,
                    'type' => 'one_time',
                    'status' => 'confirmed_payment',
                    'paid_at' => now()->subDays(2),
                ],
            );

            Donation::query()->updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'donor_email' => 'donor@example.com',
                    'amount' => 200.00,
                ],
                [
                    'donor_name' => 'Anonymous Donor',
                    'message' => 'Wishing you all the best!',
                    'show_name' => false,
                    'type' => 'one_time',
                    'status' => 'confirmed_payment',
                    'paid_at' => now()->subDay(),
                ],
            );
        }
    }

    private function seedCoverImage(string $filename): string
    {
        $sourcePath = public_path("images/campaigns/{$filename}");
        $destination = "campaigns/{$filename}";

        Storage::disk('public')->put(
            $destination,
            file_get_contents($sourcePath),
        );

        return $destination;
    }
}
