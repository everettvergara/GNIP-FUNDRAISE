<?php

namespace App\Filament\Resources\Donations\Schemas;

use App\Filament\Resources\PaymentReleases\PaymentReleaseResource;
use App\Models\Donation;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;

class DonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('campaign_id')
                    ->relationship('campaign', 'title')
                    ->required()
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false),
                TextInput::make('donor_name')
                    ->required()
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false),
                TextInput::make('donor_email')
                    ->email()
                    ->required()
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false),
                Textarea::make('message')
                    ->rows(3)
                    ->maxLength(1000)
                    ->columnSpanFull()
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false),
                Toggle::make('show_name')
                    ->label('Show as contributor')
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('₱')
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false),
                Select::make('type')
                    ->options(Donation::TYPES)
                    ->required()
                    ->default(Donation::TYPE_ONE_TIME)
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false),
                Select::make('status')
                    ->options(Donation::STATUSES)
                    ->required()
                    ->default(Donation::STATUS_PENDING)
                    ->live()
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false)
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if ($state === Donation::STATUS_CONFIRMED) {
                            $set('paid_at', now());
                        }
                    }),
                TextInput::make('xendit_invoice_id')
                    ->default(null)
                    ->disabled(),
                DateTimePicker::make('paid_at')
                    ->disabled(fn (?Donation $record): bool => $record?->isReleased() ?? false),
                Placeholder::make('payment_release')
                    ->label('Payment release')
                    ->content(function (?Donation $record): HtmlString|string {
                        if (! $record?->isReleased()) {
                            return 'Not yet released';
                        }

                        $release = $record->paymentReleases()->first();

                        if (! $release) {
                            return 'Not yet released';
                        }

                        $url = PaymentReleaseResource::getUrl('view', ['record' => $release]);

                        return new HtmlString(
                            '<a href="'.e($url).'" class="text-primary-600 hover:underline">'.
                            e($release->control_number).
                            '</a>',
                        );
                    })
                    ->visible(fn (?Donation $record): bool => (bool) $record?->isReleased()),
            ]);
    }
}
