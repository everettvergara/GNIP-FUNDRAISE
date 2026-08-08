<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignEvent extends Model
{
    public const TYPE_SUBMITTED = 'submitted';

    public const TYPE_WITHDRAWN = 'withdrawn';

    public const TYPE_APPROVED = 'approved';

    public const TYPE_REJECTED = 'rejected';

    public const TYPE_IMPACT_REPORT = 'impact_report';

    public const TYPE_REVOKED = 'revoked';

    public const TYPE_LABELS = [
        self::TYPE_SUBMITTED => 'Submitted for review',
        self::TYPE_WITHDRAWN => 'Submission withdrawn',
        self::TYPE_APPROVED => 'Approved',
        self::TYPE_REJECTED => 'Rejected',
        self::TYPE_REVOKED => 'Revoked',
        self::TYPE_IMPACT_REPORT => 'Impact story posted',
    ];

    protected $fillable = [
        'campaign_id',
        'type',
        'comment',
        'user_id',
        'admin_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function actorName(): string
    {
        if ($this->admin_id) {
            return $this->admin?->name ?? 'Admin';
        }

        if ($this->user_id) {
            $name = $this->user?->full_name;

            return $name !== '' && $name !== null ? $name : 'Fundraiser';
        }

        return 'System';
    }

    public function commentLabel(): ?string
    {
        return match ($this->type) {
            self::TYPE_SUBMITTED => 'Fundraiser comment',
            self::TYPE_REJECTED => 'Rejection message',
            self::TYPE_REVOKED => 'Revocation reason',
            self::TYPE_IMPACT_REPORT => 'Impact story',
            default => null,
        };
    }
}
