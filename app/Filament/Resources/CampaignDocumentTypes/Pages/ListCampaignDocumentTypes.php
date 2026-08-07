<?php

namespace App\Filament\Resources\CampaignDocumentTypes\Pages;

use App\Filament\Resources\CampaignDocumentTypes\CampaignDocumentTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampaignDocumentTypes extends ListRecords
{
    protected static string $resource = CampaignDocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
