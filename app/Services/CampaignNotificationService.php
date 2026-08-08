<?php

namespace App\Services;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\URL;
use Throwable;

class CampaignNotificationService
{
    public function __construct(
        private readonly EmailTemplateMailer $mailer,
    ) {}

    public function sendSubmittedToAdmin(Campaign $campaign): void
    {
        $notificationEmail = SiteSetting::current()->notification_email;

        if (! $notificationEmail) {
            report(new \RuntimeException('Site notification email is not configured.'));

            return;
        }

        $user = $campaign->user;

        $this->sendSafely('campaign_submitted_admin', $notificationEmail, [
            'campaign_title' => $campaign->title,
            'fundraiser_name' => $user?->full_name ?? 'Unknown',
            'fundraiser_email' => $user?->email ?? 'Unknown',
            'submitted_at' => $campaign->submitted_at?->format('M j, Y g:i A') ?? now()->format('M j, Y g:i A'),
            'admin_review_url' => CampaignResource::getUrl('view', ['record' => $campaign]),
        ]);
    }

    public function sendPublished(Campaign $campaign): void
    {
        $user = $campaign->user;

        if (! $user) {
            return;
        }

        $this->sendSafely('campaign_published', $user->email, [
            'name' => $user->full_name,
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

        $this->sendSafely('campaign_rejected', $user->email, [
            'name' => $user->full_name,
            'campaign_title' => $campaign->title,
            'rejection_reason' => $campaign->rejection_reason ?? 'No reason provided.',
            'edit_url' => URL::route('campaigns.edit', $campaign->slug),
        ]);
    }

    public function sendRevoked(Campaign $campaign): void
    {
        $user = $campaign->user;

        if (! $user) {
            return;
        }

        $this->sendSafely('campaign_revoked', $user->email, [
            'name' => $user->full_name,
            'campaign_title' => $campaign->title,
            'revocation_reason' => $campaign->revocation_reason ?? 'No reason provided.',
            'edit_url' => URL::route('campaigns.edit', $campaign->slug),
        ]);
    }

    /**
     * @param  array<string, string>  $placeholders
     */
    private function sendSafely(string $templateKey, string $to, array $placeholders): void
    {
        try {
            $this->mailer->send($templateKey, $to, $placeholders);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
