<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Filament\Resources\Campaigns\Concerns\HasCampaignReviewActions;
use App\Models\Campaign;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaign extends EditRecord
{
    use HasCampaignReviewActions;

    protected static string $resource = CampaignResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getRecord()->load([
            'events.user',
            'events.admin',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getCampaignReviewActions(),
            DeleteAction::make()
                ->visible(fn (Campaign $record): bool => $record->canBeDeleted()),
        ];
    }
}
