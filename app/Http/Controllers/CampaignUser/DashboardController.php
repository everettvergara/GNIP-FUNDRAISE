<?php

namespace App\Http\Controllers\CampaignUser;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $campaignIds = $user->campaigns()->pluck('id');

        $stats = [
            'campaigns_count' => $user->campaigns()->count(),
            'active_campaigns' => $user->campaigns()->where('status', 'active')->count(),
            'total_raised' => $user->campaigns()->sum('raised_amount'),
            'donations_count' => Donation::query()
                ->whereIn('campaign_id', $campaignIds)
                ->where('status', Donation::STATUS_CONFIRMED)
                ->count(),
            'recent_donations' => Donation::query()
                ->with('campaign')
                ->whereIn('campaign_id', $campaignIds)
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('campaign-user.dashboard', compact('stats'));
    }

    public function donations(): View
    {
        $campaignIds = auth()->user()->campaigns()->pluck('id');

        $donations = Donation::query()
            ->with('campaign')
            ->whereIn('campaign_id', $campaignIds)
            ->latest()
            ->paginate(20);

        return view('campaign-user.donations', compact('donations'));
    }
}
