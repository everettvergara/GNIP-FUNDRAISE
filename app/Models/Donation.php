<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_CONFIRMED = 'confirmed_payment';

    public const TYPE_ONE_TIME = 'one_time';

    public const TYPE_MONTHLY_3 = 'monthly_3';

    public const TYPE_MONTHLY_6 = 'monthly_6';

    public const TYPE_MONTHLY_12 = 'monthly_12';

    public const PRESET_AMOUNTS = [100, 200, 300, 500, 1000];

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_CONFIRMED => 'Confirmed Payment',
    ];

    public const TYPES = [
        self::TYPE_ONE_TIME => 'One-time',
        self::TYPE_MONTHLY_3 => 'Monthly (3 months)',
        self::TYPE_MONTHLY_6 => 'Monthly (6 months)',
        self::TYPE_MONTHLY_12 => 'Monthly (12 months)',
    ];

    protected $fillable = [
        'campaign_id',
        'donor_name',
        'donor_email',
        'message',
        'show_name',
        'amount',
        'type',
        'status',
        'xendit_invoice_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'show_name' => 'boolean',
        ];
    }

    public function hasDonorConsent(): bool
    {
        return (bool) $this->show_name;
    }

    public function donorDisplayName(): string
    {
        return $this->hasDonorConsent() ? $this->donor_name : 'Anonymous';
    }

    public function donorDisplayEmail(): ?string
    {
        return $this->hasDonorConsent() ? $this->donor_email : null;
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function recurringDonation(): HasOne
    {
        return $this->hasOne(RecurringDonation::class);
    }

    public function sponsorMatches(): HasMany
    {
        return $this->hasMany(DonorSponsorMatch::class);
    }

    public function paymentReleases(): BelongsToMany
    {
        return $this->belongsToMany(PaymentRelease::class, 'donation_payment_release');
    }

    public function isReleased(): bool
    {
        return $this->paymentReleases()->exists();
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeUnreleased(Builder $query): Builder
    {
        return $query->whereDoesntHave('paymentReleases');
    }

    public function scopeEligibleForRelease(Builder $query): Builder
    {
        return $query->confirmed()->unreleased();
    }

    public function commitmentMonths(): int
    {
        return match ($this->type) {
            self::TYPE_MONTHLY_3, 'recurring_3_months' => 3,
            self::TYPE_MONTHLY_6, 'recurring_6_months' => 6,
            self::TYPE_MONTHLY_12 => 12,
            default => 1,
        };
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? str_replace('_', ' ', $this->type);
    }

    public function countableAmount(): float
    {
        return (float) $this->amount * $this->commitmentMonths();
    }

    public function countsTowardRaisedAmount(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }
}
