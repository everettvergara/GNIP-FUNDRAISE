<?php

namespace App\Models;

use App\Http\Requests\StorePaymentReleaseRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class PaymentRelease extends Model
{
    protected $fillable = [
        'campaign_id',
        'control_number',
        'amount_released',
        'released_at',
        'released_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'amount_released' => 'decimal:2',
            'released_at' => 'date',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'released_by');
    }

    public function donations(): BelongsToMany
    {
        return $this->belongsToMany(Donation::class, 'donation_payment_release');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<int>  $donationIds
     */
    public static function createWithDonations(array $attributes, array $donationIds): self
    {
        $payload = array_merge($attributes, [
            'donation_ids' => $donationIds,
        ]);

        $request = StorePaymentReleaseRequest::createFrom(request()->merge($payload));
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->validateResolved();
        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $request): self {
            $release = self::query()->create([
                'campaign_id' => $validated['campaign_id'],
                'control_number' => $validated['control_number'],
                'amount_released' => $request->validatedAmountReleased(),
                'released_at' => $validated['released_at'],
                'released_by' => $validated['released_by'] ?? auth('admin')->id(),
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $release->donations()->attach($validated['donation_ids']);

            return $release;
        });
    }
}
