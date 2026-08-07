<?php

namespace App\Filament\Resources\Campaigns\Tables;

use App\Models\Campaign;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('goal_amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('raised_amount')
                    ->money('PHP')
                    ->sortable(),
                ImageColumn::make('cover_image')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Campaign::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Campaign::STATUS_PENDING => 'warning',
                        Campaign::STATUS_ACTIVE => 'success',
                        Campaign::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Campaign::STATUSES)
                    ->default(Campaign::STATUS_PENDING),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $blocked = $records->filter(fn (Campaign $campaign): bool => ! $campaign->canBeDeleted());

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Cannot delete campaigns that have donations')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $records->each->delete();
                        }),
                ]),
            ]);
    }
}
