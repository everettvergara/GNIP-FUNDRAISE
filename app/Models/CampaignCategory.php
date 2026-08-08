<?php

namespace App\Models;

use App\Support\ReferenceDataCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'category_id');
    }

    protected static function booted(): void
    {
        static::saved(fn () => ReferenceDataCache::forgetCampaignCategories());
        static::deleted(fn () => ReferenceDataCache::forgetCampaignCategories());
    }
}
