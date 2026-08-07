<?php

namespace App\Filament\Resources\PaymentReleases\Pages;

use App\Filament\Resources\PaymentReleases\PaymentReleaseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentRelease extends ViewRecord
{
    protected static string $resource = PaymentReleaseResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getRecord()->load(['donations', 'campaign', 'releasedBy']);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
