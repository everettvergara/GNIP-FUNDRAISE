<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Admin;
use App\Models\Campaign;
use App\Services\CampaignReviewService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class PendingCampaignsCards extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.pending-campaigns-cards';

    public static function canView(): bool
    {
        $admin = Filament::auth()->user();

        return $admin instanceof Admin && $admin->canAccessModule('dashboard');
    }

    /**
     * @return Collection<int, Campaign>
     */
    public function getPendingCampaigns(): Collection
    {
        return Campaign::query()
            ->with(['user', 'category', 'documents.documentType', 'events.user', 'events.admin'])
            ->where('status', Campaign::STATUS_PENDING)
            ->latest('submitted_at')
            ->limit(10)
            ->get();
    }

    public function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Approve & Publish')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->iconButton()
            ->tooltip('Approve & Publish')
            ->requiresConfirmation()
            ->modalHeading('Approve campaign')
            ->modalDescription('This will publish the campaign and email the owner.')
            ->action(function (array $arguments, CampaignReviewService $reviewService): void {
                $campaign = $this->resolveCampaignFromArguments($arguments);

                if (! $campaign) {
                    return;
                }

                $admin = Filament::auth()->user();

                if (! $admin) {
                    return;
                }

                $reviewService->approve($campaign, $admin);

                Notification::make()
                    ->title('Campaign approved and published')
                    ->success()
                    ->send();
            });
    }

    public function rejectAction(): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->iconButton()
            ->tooltip('Reject')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Rejection reason')
                    ->required()
                    ->rows(4),
            ])
            ->modalHeading('Reject campaign')
            ->modalDescription('Provide a clear reason so the fundraiser knows what to fix before resubmitting.')
            ->modalSubmitActionLabel('Reject campaign')
            ->action(function (array $arguments, array $data, CampaignReviewService $reviewService): void {
                $campaign = $this->resolveCampaignFromArguments($arguments);

                if (! $campaign) {
                    return;
                }

                $admin = Filament::auth()->user();

                if (! $admin) {
                    return;
                }

                $reviewService->reject($campaign, $admin, $data['rejection_reason']);

                Notification::make()
                    ->title('Campaign rejected')
                    ->body('The fundraiser has been notified by email.')
                    ->success()
                    ->send();
            });
    }

    public function campaignActionButton(string $actionName, int $campaignId): HtmlString
    {
        $action = $this->getAction($actionName, isMounting: false);

        if (! $action) {
            return new HtmlString('');
        }

        return new HtmlString(
            $action(['campaignId' => $campaignId])->toHtml()
        );
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    private function resolveCampaignFromArguments(array $arguments): ?Campaign
    {
        $campaignId = $arguments['campaignId'] ?? null;

        if (! $campaignId) {
            return null;
        }

        $campaign = Campaign::query()->find($campaignId);

        if (! $campaign || $campaign->status !== Campaign::STATUS_PENDING) {
            return null;
        }

        return $campaign;
    }

    protected function getViewData(): array
    {
        return [
            'campaigns' => $this->getPendingCampaigns(),
            'campaignsIndexUrl' => CampaignResource::getUrl('index'),
        ];
    }
}
