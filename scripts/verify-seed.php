<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin;
use App\Models\Campaign;
use App\Models\Sector;
use Illuminate\Support\Facades\Storage;

$errors = [];

if (Admin::query()->where('email', 'admin@goodneighbors.ph')->doesntExist()) {
    $errors[] = 'Missing admin@goodneighbors.ph';
}

$expectedSlugs = [
    'books-for-barangay-learners',
    'community-health-outreach',
    'safe-spaces-for-children',
    'typhoon-relief-fund',
    'livelihood-starter-kits',
];

$seededCampaigns = Campaign::query()
    ->whereIn('slug', $expectedSlugs)
    ->get(['slug', 'cover_image']);

if ($seededCampaigns->count() !== count($expectedSlugs)) {
    $missing = array_diff($expectedSlugs, $seededCampaigns->pluck('slug')->all());
    $errors[] = 'Missing seeded campaigns: '.implode(', ', $missing);
}

foreach ($seededCampaigns as $campaign) {
    if (! $campaign->cover_image || ! Storage::disk('public')->exists($campaign->cover_image)) {
        $errors[] = "Missing campaign cover in storage: {$campaign->slug}";
    }
}

$sectorCount = Sector::query()->count();
if ($sectorCount !== 6) {
    $errors[] = "Expected 6 sectors, found {$sectorCount}";
}

$staticAssets = [
    'public/images/design/hero-banner.png',
    'public/images/design/logo-paper-plane.png',
    'public/images/design/pre-footer-banner.png',
    'public/images/partners/unicef.png',
    'public/images/sectors/education.png',
];

foreach ($staticAssets as $asset) {
    if (! is_file(__DIR__.'/../'.$asset)) {
        $errors[] = "Missing static asset: {$asset}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Seed verification failed:\n- ".implode("\n- ", $errors)."\n");
    exit(1);
}

echo 'Seed verification passed: '.count($expectedSlugs).' demo campaigns, '.$sectorCount.' sectors, all cover images and key static assets present.'.PHP_EOL;
