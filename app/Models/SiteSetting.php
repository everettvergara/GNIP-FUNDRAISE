<?php

namespace App\Models;

use App\Support\ReferenceDataCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSetting extends Model
{
    protected $fillable = [
        'notification_email',
        'updated_by',
    ];

    protected static function booted(): void
    {
        $forget = function (): void {
            ReferenceDataCache::forgetSiteSettings();
        };

        static::saved($forget);
        static::deleted($forget);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public static function current(): self
    {
        return ReferenceDataCache::siteSettings();
    }
}
