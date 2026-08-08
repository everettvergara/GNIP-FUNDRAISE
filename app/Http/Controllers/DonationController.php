<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Rules\RecaptchaV3;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function create(string $slug): View
    {
        $campaign = Campaign::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return view('donations.create', compact('campaign'));
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $campaign = Campaign::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            'donor_name' => ['required', 'string', 'max:255'],
            'donor_email' => ['required', 'string', 'email', 'max:255'],
            'amount_option' => ['required', 'in:'.implode(',', [...Donation::PRESET_AMOUNTS, 'custom'])],
            'custom_amount' => ['required_if:amount_option,custom', 'nullable', 'numeric', 'min:100'],
            'message' => ['nullable', 'string', 'max:1000'],
            'show_name' => ['sometimes', 'boolean'],
            'type' => ['required', 'in:'.implode(',', array_keys(Donation::TYPES))],
            'g-recaptcha-response' => RecaptchaV3::rules('donate'),
        ]);

        $amount = $validated['amount_option'] === 'custom'
            ? (float) $validated['custom_amount']
            : (float) $validated['amount_option'];

        Donation::query()->create([
            'campaign_id' => $campaign->id,
            'donor_name' => $validated['donor_name'],
            'donor_email' => $validated['donor_email'],
            'message' => $validated['message'] ?? null,
            'show_name' => $request->boolean('show_name'),
            'amount' => $amount,
            'type' => $validated['type'],
            'status' => Donation::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);

        return redirect()
            ->route('campaigns.show', $campaign->slug)
            ->with('status', 'donation-confirmed');
    }
}
