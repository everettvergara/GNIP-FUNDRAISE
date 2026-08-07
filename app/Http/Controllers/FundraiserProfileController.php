<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\View\View;

class FundraiserProfileController extends Controller
{
    public function show(User $user): View
    {
        if (! $user->hasPublicProfile()) {
            abort(404);
        }

        $campaigns = Campaign::query()
            ->with('category')
            ->where('user_id', $user->id)
            ->where('status', Campaign::STATUS_ACTIVE)
            ->latest()
            ->get();

        return view('fundraisers.show', [
            'user' => $user,
            'campaigns' => $campaigns,
        ]);
    }
}
