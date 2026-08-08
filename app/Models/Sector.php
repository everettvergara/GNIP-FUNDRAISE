<?php

namespace App\Models;

use App\Support\ReferenceDataCache;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    public const CATEGORY_QUALITY_OF_LIFE = 'Improve the Quality of Life and Promote Child Rights';

    public const CATEGORY_REDUCE_INEQUALITY = 'Reduce Inequality and Foster Resilience in Communities';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'image',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => ReferenceDataCache::forgetSectors());
        static::deleted(fn () => ReferenceDataCache::forgetSectors());
    }
}
