<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class CampaignPolicy
{
    public function create(User $user): bool
    {
        return $user->canAccessModule('campaigns_create');
    }

    public function view(Authenticatable $user, Campaign $campaign): bool
    {
        if ($this->isActiveAdmin($user)) {
            return true;
        }

        return $user instanceof User && $user->id === $campaign->user_id;
    }

    public function update(Authenticatable $user, Campaign $campaign): bool
    {
        if ($this->isActiveAdmin($user)) {
            return true;
        }

        return $user instanceof User
            && $user->canAccessModule('campaigns_manage')
            && $user->id === $campaign->user_id
            && $campaign->canBeEditedByOwner();
    }

    public function uploadDocument(Authenticatable $user, Campaign $campaign): bool
    {
        if ($this->isActiveAdmin($user)) {
            return true;
        }

        return $user instanceof User
            && $user->canAccessModule('campaigns_manage')
            && $user->id === $campaign->user_id
            && $campaign->canBeEditedByOwner();
    }

    public function submitForApproval(Authenticatable $user, Campaign $campaign): bool
    {
        if ($this->isActiveAdmin($user)) {
            return true;
        }

        return $user instanceof User
            && $user->canAccessModule('campaigns_manage')
            && $user->id === $campaign->user_id
            && $campaign->canSubmitForApproval();
    }

    public function withdrawSubmission(Authenticatable $user, Campaign $campaign): bool
    {
        if ($this->isActiveAdmin($user)) {
            return false;
        }

        return $user instanceof User
            && $user->canAccessModule('campaigns_manage')
            && $user->id === $campaign->user_id
            && $campaign->canWithdrawSubmission();
    }

    public function delete(Authenticatable $user, Campaign $campaign): bool
    {
        if ($this->isActiveAdmin($user)) {
            return true;
        }

        return $user instanceof User
            && $user->canAccessModule('campaigns_manage')
            && $user->id === $campaign->user_id
            && $campaign->canBeDeleted();
    }

    private function isActiveAdmin(Authenticatable $user): bool
    {
        return $user instanceof Admin && $user->is_active;
    }
}
