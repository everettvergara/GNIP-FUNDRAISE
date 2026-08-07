<?php

namespace App\Http\Controllers\CampaignUser;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MyCampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = auth()->user()
            ->campaigns()
            ->with('category')
            ->withCount('donations')
            ->latest()
            ->paginate(12);

        return view('campaign-user.my-campaigns', compact('campaigns'));
    }
}
