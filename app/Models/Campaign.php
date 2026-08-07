<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Campaign extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_ENDED = 'ended';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PENDING => 'Pending review',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_PAUSED => 'Paused',
        self::STATUS_ENDED => 'Ended',
    ];

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'goal_amount',
        'raised_amount',
        'cover_image',
        'thank_you_message',
        'status',
        'is_featured',
        'starts_at',
        'ends_at',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'goal_amount' => 'decimal:2',
            'raised_amount' => 'decimal:2',
            'is_featured' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CampaignCategory::class, 'category_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function media(): HasMany
    {
        return $this->hasMany(CampaignMedia::class)->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CampaignDocument::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function recurringDonations(): HasMany
    {
        return $this->hasMany(RecurringDonation::class);
    }

    public function paymentReleases(): HasMany
    {
        return $this->hasMany(PaymentRelease::class);
    }

    public function impactReports(): HasMany
    {
        return $this->hasMany(CampaignImpactReport::class)->latest();
    }

    public function canBeDeleted(): bool
    {
        return ! $this->donations()->exists();
    }

    public function canBeEditedByOwner(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED], true);
    }

    public function hasAllRequiredDocuments(): bool
    {
        return $this->missingRequiredDocumentTypes()->isEmpty();
    }

    public function missingRequiredDocumentTypes(): Collection
    {
        $requiredTypeIds = CampaignDocumentType::requiredActive()->pluck('id');

        if ($requiredTypeIds->isEmpty()) {
            return new Collection();
        }

        $uploadedTypeIds = $this->documents()
            ->whereIn('document_type_id', $requiredTypeIds)
            ->pluck('document_type_id');

        $missingIds = $requiredTypeIds->diff($uploadedTypeIds);

        return CampaignDocumentType::query()
            ->whereIn('id', $missingIds)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function canSubmitForApproval(): bool
    {
        return $this->canBeEditedByOwner() && $this->hasAllRequiredDocuments();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending review',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_ENDED => 'Ended',
            'completed' => 'Ended',
            default => ucfirst($this->status),
        };
    }

    public function recalculateRaisedAmount(): void
    {
        $total = $this->donations()
            ->where('status', Donation::STATUS_CONFIRMED)
            ->get()
            ->sum(fn (Donation $donation): float => $donation->countableAmount());

        $this->updateQuietly(['raised_amount' => $total]);
    }

    protected static function booted(): void
    {
        static::deleting(function (Campaign $campaign): void {
            if ($campaign->cover_image) {
                Storage::disk('public')->delete($campaign->cover_image);
            }

            $campaign->media()->get()->each(function (CampaignMedia $media): void {
                Storage::disk('public')->delete($media->path);
            });

            $campaign->documents()->get()->each(function (CampaignDocument $document): void {
                $document->delete();
            });

            $campaign->impactReports()->get()->each(function (CampaignImpactReport $report): void {
                $report->photos()->get()->each(function (CampaignImpactPhoto $photo): void {
                    $photo->delete();
                });

                $report->delete();
            });
        });
    }
}
