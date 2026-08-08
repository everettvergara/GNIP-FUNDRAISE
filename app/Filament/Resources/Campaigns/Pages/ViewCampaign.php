<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Filament\Resources\Campaigns\Concerns\HasCampaignReviewActions;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaign extends ViewRecord
{
    use HasCampaignReviewActions;

    protected static string $resource = CampaignResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getRecord()->load([
            'user',
            'category',
            'media',
            'documents.documentType',
            'reviewer',
            'events.user',
            'events.admin',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return $this->getCampaignReviewActions();
    }
}
