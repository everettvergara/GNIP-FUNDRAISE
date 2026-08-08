<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Sector;
use App\Support\ReferenceDataCache;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const SECTOR_CATEGORIES = [
        Sector::CATEGORY_QUALITY_OF_LIFE,
        Sector::CATEGORY_REDUCE_INEQUALITY,
    ];

    public function index(): View
    {
        $page = ReferenceDataCache::cmsPage('home');
        $sectorsByCategory = ReferenceDataCache::sectorsGroupedByCategory();

        return view('home', [
            'page' => $page,
            'featuredCampaigns' => Campaign::query()
                ->with('user')
                ->publiclyListed()
                ->where('is_featured', true)
                ->orderByDesc('raised_amount')
                ->take(3)
                ->get(),
            'sectorCategories' => collect(self::SECTOR_CATEGORIES)
                ->map(fn (string $label) => [
                    'label' => $label,
                    'sectors' => $sectorsByCategory->get($label, collect()),
                ])
                ->filter(fn (array $group) => $group['sectors']->isNotEmpty()),
        ]);
    }
}
