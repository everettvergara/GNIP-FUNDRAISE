<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringDonation extends Model
{
    protected $fillable = [
        'donation_id',
        'campaign_id',
        'xendit_plan_id',
        'status',
        'next_payment_at',
    ];

    protected function casts(): array
    {
        return [
            'next_payment_at' => 'date',
        ];
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
