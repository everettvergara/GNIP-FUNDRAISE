<?php

namespace App\Models;

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

    public static function activeOrdered(): \Illuminate\Database\Eloquent\Builder
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public static function requiredActive(): \Illuminate\Database\Eloquent\Builder
    {
        return static::activeOrdered()->where('is_required', true);
    }
}
