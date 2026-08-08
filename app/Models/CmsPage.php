<?php

namespace App\Models;

use App\Support\ReferenceDataCache;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'body',
        'meta_title',
        'meta_description',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'body' => 'array',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $forget = function (CmsPage $page): void {
            ReferenceDataCache::forgetCmsPage($page->slug);
        };

        static::saved($forget);
        static::deleted($forget);
    }
}
