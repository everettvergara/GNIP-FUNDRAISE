<?php

namespace App\Filament\Resources\PaymentReleases\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentReleasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('released_at', 'desc')
            ->columns([
                TextColumn::make('control_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('campaign.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount_released')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('released_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('releasedBy.name')
                    ->label('Released by')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('donations_count')
                    ->counts('donations')
                    ->label('Donations')
                    ->sortable(),
                TextColumn::make('remarks')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('campaign_id')
                    ->relationship('campaign', 'title')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('released_by')
                    ->relationship('releasedBy', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('released_at')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From'),
                        DatePicker::make('until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('released_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('released_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
