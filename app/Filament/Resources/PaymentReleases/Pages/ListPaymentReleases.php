<?php

namespace App\Filament\Resources\PaymentReleases\Pages;

use App\Filament\Resources\PaymentReleases\PaymentReleaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentReleases extends ListRecords
{
    protected static string $resource = PaymentReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
