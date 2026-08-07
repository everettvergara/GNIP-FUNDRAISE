<?php

namespace App\Filament\Resources\PaymentReleases\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentReleaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('control_number'),
                TextEntry::make('campaign.title')
                    ->label('Campaign'),
                TextEntry::make('amount_released')
                    ->label('Amount released')
                    ->money('PHP'),
                TextEntry::make('released_at')
                    ->date(),
                TextEntry::make('releasedBy.name')
                    ->label('Released by'),
                TextEntry::make('remarks')
                    ->columnSpanFull(),
                RepeatableEntry::make('donations')
                    ->label('Tagged donations')
                    ->schema([
                        TextEntry::make('donor_name'),
                        TextEntry::make('donor_email'),
                        TextEntry::make('amount')
                            ->money('PHP'),
                        TextEntry::make('paid_at')
                            ->dateTime(),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }
}
