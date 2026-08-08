<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Partner;
use App\Support\ReferenceDataCache;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CmsPageController extends Controller
{
    public function show(string $slug): View|Response
    {
        $page = ReferenceDataCache::cmsPage($slug);

        if ($page === null) {
            abort(404);
        }

        return view('cms.show', compact('page'));
    }

    public function announcements(): View
    {
        return view('cms.announcements', [
            'announcements' => Announcement::query()
                ->where('is_published', true)
                ->latest('published_at')
                ->paginate(12),
        ]);
    }

    public function sectors(): View
    {
        return view('cms.sectors', [
            'sectors' => ReferenceDataCache::sectorsOrdered(),
        ]);
    }

    public function sector(string $slug): View
    {
        $sector = ReferenceDataCache::sectorsOrdered()->firstWhere('slug', $slug);

        if ($sector === null) {
            abort(404);
        }

        return view('cms.sector', compact('sector'));
    }

    public function partners(): View
    {
        return view('cms.partners', [
            'partners' => Partner::query()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
