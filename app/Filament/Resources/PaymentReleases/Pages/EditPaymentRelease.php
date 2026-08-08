<?php

namespace App\Filament\Resources\PaymentReleases\Pages;

use App\Filament\Resources\PaymentReleases\PaymentReleaseResource;
use App\Http\Requests\UpdatePaymentReleaseRequest;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentRelease extends EditRecord
{
    protected static string $resource = PaymentReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $request = UpdatePaymentReleaseRequest::createFrom(
            request()->merge([
                ...$data,
                'id' => $this->getRecord()->getKey(),
            ]),
        );
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->validateResolved();

        return $request->validated();
    }
}
