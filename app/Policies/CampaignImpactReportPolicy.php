<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\CampaignImpactReport;
use App\Models\User;

class CampaignImpactReportPolicy
{
    public function create(User $user, Campaign $campaign): bool
    {
        return $user->canAccessModule('impact_reports')
            && $user->id === $campaign->user_id
            && $campaign->status === Campaign::STATUS_ACTIVE;
    }

    public function delete(User $user, CampaignImpactReport $report): bool
    {
        $report->loadMissing('campaign');

        return $user->canAccessModule('impact_reports')
            && $user->id === $report->campaign->user_id;
    }
}
