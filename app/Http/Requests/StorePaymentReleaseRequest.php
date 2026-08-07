<?php

namespace App\Http\Requests;

use App\Models\Donation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePaymentReleaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'control_number' => ['required', 'string', 'max:255', 'unique:payment_releases,control_number'],
            'donation_ids' => ['required', 'array', 'min:1'],
            'donation_ids.*' => ['integer', 'distinct', 'exists:donations,id'],
            'released_at' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $campaignId = (int) $this->input('campaign_id');
            $donationIds = collect($this->input('donation_ids', []))
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values();

            $donations = Donation::query()
                ->whereIn('id', $donationIds)
                ->get();

            if ($donations->count() !== $donationIds->count()) {
                $validator->errors()->add('donation_ids', 'One or more selected donations could not be found.');

                return;
            }

            $invalidCampaign = $donations->first(fn (Donation $donation): bool => $donation->campaign_id !== $campaignId);

            if ($invalidCampaign) {
                $validator->errors()->add('donation_ids', 'All selected donations must belong to the chosen campaign.');

                return;
            }

            $ineligible = $donations->first(
                fn (Donation $donation): bool => $donation->status !== Donation::STATUS_CONFIRMED || $donation->isReleased(),
            );

            if ($ineligible) {
                $validator->errors()->add('donation_ids', 'Only confirmed, unreleased donations can be included in a payment release.');

                return;
            }
        });
    }

    public function validatedAmountReleased(): string
    {
        return (string) Donation::query()
            ->whereIn('id', $this->input('donation_ids', []))
            ->sum('amount');
    }
}
