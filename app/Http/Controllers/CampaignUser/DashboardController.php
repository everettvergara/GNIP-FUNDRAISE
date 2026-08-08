<?php

namespace App\Http\Controllers\CampaignUser;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $campaignStats = $user->campaigns()
            ->selectRaw('COUNT(*) as campaigns_count')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active_campaigns', [Campaign::STATUS_ACTIVE])
            ->selectRaw('COALESCE(SUM(raised_amount), 0) as total_raised')
            ->first();

        $donationQuery = Donation::query()
            ->whereHas('campaign', fn ($query) => $query->where('user_id', $user->id));

        $stats = [
            'campaigns_count' => (int) ($campaignStats->campaigns_count ?? 0),
            'active_campaigns' => (int) ($campaignStats->active_campaigns ?? 0),
            'total_raised' => (float) ($campaignStats->total_raised ?? 0),
            'donations_count' => (clone $donationQuery)
                ->where('status', Donation::STATUS_CONFIRMED)
                ->count(),
            'recent_donations' => (clone $donationQuery)
                ->with('campaign')
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
