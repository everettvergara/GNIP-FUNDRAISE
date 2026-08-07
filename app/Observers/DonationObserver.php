<?php

namespace App\Observers;

use App\Models\Campaign;
use App\Models\Donation;

class DonationObserver
{
    public function saved(Donation $donation): void
    {
        $this->syncCampaigns($donation);
    }

    public function deleted(Donation $donation): void
    {
        $this->syncCampaigns($donation);
    }

    private function syncCampaigns(Donation $donation): void
    {
        $campaignIds = array_filter(array_unique([
            $donation->campaign_id,
            $donation->getOriginal('campaign_id'),
        ]));

        foreach ($campaignIds as $campaignId) {
            Campaign::query()->find($campaignId)?->recalculateRaisedAmount();
        }
    }
}
