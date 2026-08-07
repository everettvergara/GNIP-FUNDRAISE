<?php

namespace App\Filament\Resources\CampaignDocumentTypes\Pages;

use App\Filament\Resources\CampaignDocumentTypes\CampaignDocumentTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignDocumentType extends EditRecord
{
    protected static string $resource = CampaignDocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
