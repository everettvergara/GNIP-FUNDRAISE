<?php

namespace App\Filament\Resources\Donations\Tables;

use App\Filament\Resources\PaymentReleases\PaymentReleaseResource;
use App\Models\Donation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('campaign.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('donor_name')
                    ->searchable(),
                TextColumn::make('donor_email')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('type')
                    ->formatStateUsing(fn (string $state): string => Donation::TYPES[$state] ?? str_replace('_', ' ', $state))
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Donation::STATUSES[$state] ?? $state)
                    ->colors([
                        'warning' => Donation::STATUS_PENDING,
                        'success' => Donation::STATUS_CONFIRMED,
                        'danger' => Donation::STATUS_CANCELLED,
                    ])
                    ->searchable(),
                TextColumn::make('release_status')
                    ->label('Release')
                    ->state(function (Donation $record): string {
                        if (! $record->isReleased()) {
                            return 'Unreleased';
                        }

                        return $record->paymentReleases()->value('control_number') ?? 'Released';
                    })
                    ->url(function (Donation $record): ?string {
                        $release = $record->paymentReleases()->first();

                        if (! $release) {
                            return null;
                        }

                        return PaymentReleaseResource::getUrl('view', ['record' => $release]);
                    })
                    ->color(fn (Donation $record): string => $record->isReleased() ? 'success' : 'gray')
                    ->badge(),
                TextColumn::make('xendit_invoice_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Donation::STATUSES),
                SelectFilter::make('campaign_id')
                    ->relationship('campaign', 'title')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('release_status')
                    ->options([
                        'unreleased' => 'Unreleased',
                        'released' => 'Released',
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $value = $data['value'] ?? null;

                        if ($value === 'released') {
                            $query->whereHas('paymentReleases');
                        } elseif ($value === 'unreleased') {
                            $query->whereDoesntHave('paymentReleases');
                        }
                    }),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirm payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Donation $record): bool => $record->status === Donation::STATUS_PENDING && ! $record->isReleased())
                    ->action(function (Donation $record): void {
                        $record->update([
                            'status' => Donation::STATUS_CONFIRMED,
                            'paid_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Donation confirmed')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Donation $record): bool => $record->status === Donation::STATUS_PENDING && ! $record->isReleased())
                    ->action(function (Donation $record): void {
                        $record->update([
                            'status' => Donation::STATUS_CANCELLED,
                        ]);

                        Notification::make()
                            ->title('Donation cancelled')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
