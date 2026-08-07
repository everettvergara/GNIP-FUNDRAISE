<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\CmsPage;
use App\Models\Partner;
use App\Models\Sector;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CmsPageController extends Controller
{
    public function show(string $slug): View|Response
    {
        $page = CmsPage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

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
            'sectors' => Sector::query()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function sector(string $slug): View
    {
        $sector = Sector::query()
            ->where('slug', $slug)
            ->firstOrFail();

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
