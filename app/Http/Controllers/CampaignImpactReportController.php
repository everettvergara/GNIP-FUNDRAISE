<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignImpactReportRequest;
use App\Models\Campaign;
use App\Models\CampaignImpactReport;
use Illuminate\Http\RedirectResponse;

class CampaignImpactReportController extends Controller
{
    public function store(StoreCampaignImpactReportRequest $request, string $slug): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        if ($campaign->status !== Campaign::STATUS_ACTIVE) {
            abort(403, 'Impact reports can only be added to published campaigns.');
        }

        $this->authorize('create', [CampaignImpactReport::class, $campaign]);

        $report = $campaign->impactReports()->create([
            'message' => $request->validated('message'),
        ]);

        $sortOrder = 0;

        foreach ($request->file('photos') as $file) {
            $sortOrder++;
            $report->photos()->create([
                'path' => $file->store('campaigns/impact-reports', 'public'),
                'sort_order' => $sortOrder,
            ]);
        }

        return redirect()
            ->route('campaigns.show', $campaign->slug)
            ->with('status', 'impact-report-created');
    }

    public function destroy(string $slug, CampaignImpactReport $impactReport): RedirectResponse
    {
        $campaign = Campaign::query()->where('slug', $slug)->firstOrFail();

        abort_unless($impactReport->campaign_id === $campaign->id, 404);

        $this->authorize('delete', $impactReport);

        $impactReport->delete();

        return redirect()
            ->route('campaigns.show', $campaign->slug)
            ->with('status', 'impact-report-deleted');
    }
}
