<?php

namespace App\Filament\Resources\PaymentReleases\Schemas;

use App\Models\Donation;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PaymentReleaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('campaign_id')
                    ->relationship(
                        'campaign',
                        'title',
                        modifyQueryUsing: fn ($query) => $query->whereHas(
                            'donations',
                            fn ($donationQuery) => $donationQuery->eligibleForRelease(),
                        ),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('donation_ids', []))
                    ->visibleOn(['create']),
                CheckboxList::make('donation_ids')
                    ->label('Donations to release')
                    ->options(function (Get $get): array {
                        $campaignId = $get('campaign_id');

                        if (blank($campaignId)) {
                            return [];
                        }

                        return Donation::query()
                            ->eligibleForRelease()
                            ->where('campaign_id', $campaignId)
                            ->orderByDesc('created_at')
                            ->get()
                            ->mapWithKeys(fn (Donation $donation): array => [
                                $donation->id => sprintf(
                                    '%s — ₱%s — %s — %s',
                                    $donation->donor_name,
                                    number_format((float) $donation->amount, 2),
                                    $donation->donor_email,
                                    $donation->created_at?->format('M j, Y') ?? '',
                                ),
                            ])
                            ->all();
                    })
                    ->columns(1)
                    ->required()
                    ->live()
                    ->visibleOn(['create'])
                    ->helperText('Only confirmed, unreleased donations from the selected campaign are shown.'),
                Placeholder::make('amount_preview')
                    ->label('Amount released')
                    ->content(fn (Get $get): string => '₱'.self::calculateAmount($get('donation_ids')))
                    ->visibleOn(['create']),
                Placeholder::make('campaign_display')
                    ->label('Campaign')
                    ->content(fn ($record): string => $record?->campaign?->title ?? '—')
                    ->visibleOn(['edit']),
                TextInput::make('control_number')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('amount_released')
                    ->label('Amount released')
                    ->prefix('₱')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn(['edit']),
                DatePicker::make('released_at')
                    ->required()
                    ->default(now()),
                Placeholder::make('released_by_display')
                    ->label('Released by')
                    ->content(fn ($record): string => $record?->releasedBy?->name ?? Filament::auth()->user()?->name ?? '—')
                    ->visibleOn(['create', 'edit']),
                Textarea::make('remarks')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @param  array<int|string>|null  $donationIds
     */
    public static function calculateAmount(?array $donationIds): string
    {
        if (blank($donationIds)) {
            return '0.00';
        }

        $total = Donation::query()
            ->whereIn('id', $donationIds)
            ->sum('amount');

        return number_format((float) $total, 2, '.', '');
    }
}
