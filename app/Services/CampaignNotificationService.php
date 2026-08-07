<?php

namespace App\Services;

use App\Models\Campaign;
use Illuminate\Support\Facades\URL;

class CampaignNotificationService
{
    public function __construct(
        private readonly EmailTemplateMailer $mailer,
    ) {}

    public function sendPublished(Campaign $campaign): void
    {
        $user = $campaign->user;

        if (! $user) {
            return;
        }

        $this->mailer->send('campaign_published', $user->email, [
            'name' => $user->name,
            'campaign_title' => $campaign->title,
            'campaign_url' => URL::route('campaigns.show', $campaign->slug),
        ]);
    }

    public function sendRejected(Campaign $campaign): void
    {
        $user = $campaign->user;

        if (! $user) {
            return;
        }

        $this->mailer->send('campaign_rejected', $user->email, [
            'name' => $user->name,
            'campaign_title' => $campaign->title,
            'rejection_reason' => $campaign->rejection_reason ?? 'No reason provided.',
            'edit_url' => URL::route('campaigns.edit', $campaign->slug),
        ]);
    }
}
