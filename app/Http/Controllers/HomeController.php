<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CmsPage;
use App\Models\Partner;
use App\Models\Sector;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const SECTOR_CATEGORIES = [
        Sector::CATEGORY_QUALITY_OF_LIFE,
        Sector::CATEGORY_REDUCE_INEQUALITY,
    ];

    public function index(): View
    {
        $page = CmsPage::query()
            ->where('slug', 'home')
            ->where('is_published', true)
            ->first();

        return view('home', [
            'page' => $page,
            'featuredCampaigns' => Campaign::query()
                ->with('user')
                ->where('status', 'active')
                ->where('is_featured', true)
                ->orderByDesc('raised_amount')
                ->take(3)
                ->get(),
            'sectorCategories' => collect(self::SECTOR_CATEGORIES)
                ->map(fn (string $label) => [
                    'label' => $label,
                    'sectors' => Sector::query()
                        ->where('category', $label)
                        ->orderBy('sort_order')
                        ->get(),
                ])
                ->filter(fn (array $group) => $group['sectors']->isNotEmpty()),
            'partners' => Partner::query()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
