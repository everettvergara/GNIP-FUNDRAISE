<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignImpactReport extends Model
{
    protected $fillable = [
        'campaign_id',
        'message',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CampaignImpactPhoto::class)->orderBy('sort_order');
    }

    protected static function booted(): void
    {
        static::deleting(function (CampaignImpactReport $report): void {
            $report->photos()->get()->each(function (CampaignImpactPhoto $photo): void {
                $photo->delete();
            });
        });
    }
}
