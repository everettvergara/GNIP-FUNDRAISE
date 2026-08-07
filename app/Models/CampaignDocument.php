<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CampaignDocument extends Model
{
    protected $fillable = [
        'campaign_id',
        'document_type_id',
        'path',
        'original_name',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(CampaignDocumentType::class, 'document_type_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (CampaignDocument $document): void {
            Storage::disk('public')->delete($document->path);
        });
    }
}
