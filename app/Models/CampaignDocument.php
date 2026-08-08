<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function publicUrl(): string
    {
        return asset('storage/'.$this->path);
    }

    public function extension(): string
    {
        return Str::lower(pathinfo($this->path, PATHINFO_EXTENSION));
    }

    public function isImage(): bool
    {
        return in_array($this->extension(), ['jpg', 'jpeg', 'png'], true);
    }

    public function isPdf(): bool
    {
        return $this->extension() === 'pdf';
    }

    public function isPreviewable(): bool
    {
        return $this->isImage() || $this->isPdf();
    }

    protected static function booted(): void
    {
        static::deleting(function (CampaignDocument $document): void {
            Storage::disk('public')->delete($document->path);
        });
    }
}
