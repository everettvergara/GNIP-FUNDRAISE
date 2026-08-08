<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DenyCampaignUsersFromAdminPanel
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $webUser = Auth::guard('web')->user();

        if ($webUser instanceof User && $webUser->isCampaignPortalAccount()) {
            if ($request->expectsJson()) {
                abort(403, 'Campaign users cannot access the admin panel.');
            }

            return redirect()
                ->route('dashboard')
                ->with('error', 'Campaign accounts cannot access the admin panel.');
        }

        return $next($request);
    }
}
