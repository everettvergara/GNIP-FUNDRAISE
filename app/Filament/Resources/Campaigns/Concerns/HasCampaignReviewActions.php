<?php

namespace App\Filament\Resources\Campaigns\Concerns;

use App\Models\Campaign;
use App\Models\CampaignDocumentType;
use App\Services\CampaignReviewService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

trait HasCampaignReviewActions
{
    protected function getCampaignReviewActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve & Publish')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve campaign')
                ->modalDescription('This will publish the campaign and email the owner.')
                ->visible(fn (Campaign $record): bool => $record->status === Campaign::STATUS_PENDING)
                ->action(function (Campaign $record, CampaignReviewService $reviewService): void {
                    $admin = Filament::auth()->user();

                    if (! $admin) {
                        return;
                    }

                    $reviewService->approve($record, $admin);

                    Notification::make()
                        ->title('Campaign approved and published')
                        ->success()
                        ->send();
                }),
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (Campaign $record): bool => $record->status === Campaign::STATUS_PENDING)
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Rejection reason')
                        ->required()
                        ->rows(4),
                ])
                ->action(function (array $data, Campaign $record, CampaignReviewService $reviewService): void {
                    $admin = Filament::auth()->user();

                    if (! $admin) {
                        return;
                    }

                    $reviewService->reject($record, $admin, $data['rejection_reason']);

                    Notification::make()
                        ->title('Campaign rejected')
                        ->success()
                        ->send();
                }),
        ];
    }
}
