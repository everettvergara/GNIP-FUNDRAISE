<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CampaignImpactPhoto extends Model
{
    protected $fillable = [
        'campaign_impact_report_id',
        'path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function impactReport(): BelongsTo
    {
        return $this->belongsTo(CampaignImpactReport::class, 'campaign_impact_report_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (CampaignImpactPhoto $photo): void {
            Storage::disk('public')->delete($photo->path);
        });
    }
}
