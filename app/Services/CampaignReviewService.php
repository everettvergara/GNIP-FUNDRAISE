<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Campaign;
use App\Models\CampaignEvent;
use Illuminate\Support\Facades\Request;

class CampaignReviewService
{
    public function __construct(
        private readonly CampaignNotificationService $notifications,
    ) {}

    public function approve(Campaign $campaign, Admin $admin): void
    {
        $previousStatus = $campaign->status;

        $campaign->update([
            'status' => Campaign::STATUS_ACTIVE,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->logActivity($admin, 'campaign.approved', $campaign, [
            'from_status' => $previousStatus,
            'to_status' => Campaign::STATUS_ACTIVE,
        ]);

        $campaign->events()->create([
            'type' => CampaignEvent::TYPE_APPROVED,
            'admin_id' => $admin->id,
        ]);

        $this->notifications->sendPublished($campaign);
    }

    public function reject(Campaign $campaign, Admin $admin, string $rejectionReason): void
    {
        $previousStatus = $campaign->status;

        $campaign->update([
            'status' => Campaign::STATUS_REJECTED,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'rejection_reason' => $rejectionReason,
        ]);

        $this->logActivity($admin, 'campaign.rejected', $campaign, [
            'from_status' => $previousStatus,
            'to_status' => Campaign::STATUS_REJECTED,
            'rejection_reason' => $rejectionReason,
        ]);

        $campaign->events()->create([
            'type' => CampaignEvent::TYPE_REJECTED,
            'comment' => $rejectionReason,
            'admin_id' => $admin->id,
        ]);

        $this->notifications->sendRejected($campaign);
    }

    public function revoke(Campaign $campaign, Admin $admin, string $revocationReason): void
    {
        $previousStatus = $campaign->status;

        $campaign->update([
            'status' => Campaign::STATUS_PAUSED,
            'revocation_reason' => $revocationReason,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $this->logActivity($admin, 'campaign.revoked', $campaign, [
            'from_status' => $previousStatus,
            'to_status' => Campaign::STATUS_PAUSED,
            'revocation_reason' => $revocationReason,
        ]);

        $campaign->events()->create([
            'type' => CampaignEvent::TYPE_REVOKED,
            'comment' => $revocationReason,
            'admin_id' => $admin->id,
        ]);

        $this->notifications->sendRevoked($campaign);
    }

    private function logActivity(Admin $admin, string $action, Campaign $campaign, array $changes): void
    {
        ActivityLog::query()->create([
            'admin_id' => $admin->id,
            'action' => $action,
            'model' => 'Campaign',
            'model_id' => $campaign->id,
            'changes' => $changes,
            'ip' => Request::ip(),
        ]);
    }
}
