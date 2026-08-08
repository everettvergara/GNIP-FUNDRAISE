<?php

namespace App\Filament\Resources\Campaigns\Concerns;

use App\Models\Admin;
use App\Models\Campaign;
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
                ->visible(fn (Campaign $record): bool => $record->status === Campaign::STATUS_PENDING && $this->adminCanManageCampaigns())
                ->action(function (Campaign $record, CampaignReviewService $reviewService): void {
                    $admin = Filament::auth()->user();

                    if (! $admin) {
                        return;
                    }

                    $reviewService->approve($record, $admin);
                    $this->refreshCampaignReviewState($record);

                    Notification::make()
                        ->title('Campaign approved and published')
                        ->success()
                        ->send();
                }),
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->modalHeading('Reject campaign')
                ->modalDescription('Provide a clear reason so the fundraiser knows what to fix before resubmitting.')
                ->modalSubmitActionLabel('Reject campaign')
                ->visible(fn (Campaign $record): bool => $record->status === Campaign::STATUS_PENDING && $this->adminCanManageCampaigns())
                ->schema([
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
                    $this->refreshCampaignReviewState($record);

                    Notification::make()
                        ->title('Campaign rejected')
                        ->body('The fundraiser has been notified by email.')
                        ->success()
                        ->send();
                }),
            Action::make('revoke')
                ->label('Revoke campaign')
                ->icon('heroicon-o-no-symbol')
                ->color('warning')
                ->modalHeading('Revoke published campaign')
                ->modalDescription('This will remove the campaign from public listing. The fundraiser will be notified by email.')
                ->modalSubmitActionLabel('Revoke campaign')
                ->visible(fn (Campaign $record): bool => $record->canBeRevokedByAdmin() && $this->adminCanManageCampaigns())
                ->schema([
                    Textarea::make('revocation_reason')
                        ->label('Revocation reason')
                        ->required()
                        ->rows(4),
                ])
                ->action(function (array $data, Campaign $record, CampaignReviewService $reviewService): void {
                    $admin = Filament::auth()->user();

                    if (! $admin) {
                        return;
                    }

                    $reviewService->revoke($record, $admin, $data['revocation_reason']);
                    $this->refreshCampaignReviewState($record);

                    Notification::make()
                        ->title('Campaign revoked')
                        ->body('The campaign is no longer publicly listed. The fundraiser has been notified.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function adminCanManageCampaigns(): bool
    {
        $admin = Filament::auth()->user();

        return $admin instanceof Admin && $admin->canAccessModule('campaigns');
    }

    protected function refreshCampaignReviewState(Campaign $record): void
    {
        $record->refresh();
        $record->load([
            'user',
            'category',
            'media',
            'documents.documentType',
            'reviewer',
            'events.user',
            'events.admin',
        ]);

        if (! property_exists($this, 'record') || ! $this->record instanceof Campaign) {
            return;
        }

        if ($this->record->is($record)) {
            $this->record = $record;
        }
    }
}
