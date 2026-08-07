<?php

namespace App\Filament\Resources\PaymentReleases\Pages;

use App\Filament\Resources\PaymentReleases\PaymentReleaseResource;
use App\Models\PaymentRelease;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePaymentRelease extends CreateRecord
{
    protected static string $resource = PaymentReleaseResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        $donationIds = array_map('intval', $data['donation_ids'] ?? []);

        return PaymentRelease::createWithDonations([
            'campaign_id' => $data['campaign_id'],
            'control_number' => $data['control_number'],
            'released_at' => $data['released_at'],
            'released_by' => auth('admin')->id(),
            'remarks' => $data['remarks'] ?? null,
        ], $donationIds);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Payment release created')
            ->body('Control number: '.$this->record->control_number)
            ->success();
    }
}
