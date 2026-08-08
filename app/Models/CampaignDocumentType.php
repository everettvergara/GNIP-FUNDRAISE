<?php

namespace App\Models;

use App\Support\ReferenceDataCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignDocumentType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CampaignDocument::class, 'document_type_id');
    }

    public static function activeOrdered(): Builder
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public static function requiredActive(): Builder
    {
        return static::activeOrdered()->where('is_required', true);
    }

    protected static function booted(): void
    {
        static::saved(fn () => ReferenceDataCache::forgetDocumentTypes());
        static::deleted(fn () => ReferenceDataCache::forgetDocumentTypes());
    }
}
