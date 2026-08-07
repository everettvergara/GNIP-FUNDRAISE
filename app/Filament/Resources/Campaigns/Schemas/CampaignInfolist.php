<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Campaign;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Campaign::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Campaign::STATUS_DRAFT => 'gray',
                        Campaign::STATUS_PENDING => 'warning',
                        Campaign::STATUS_ACTIVE => 'success',
                        Campaign::STATUS_REJECTED => 'danger',
                        Campaign::STATUS_PAUSED => 'gray',
                        Campaign::STATUS_ENDED => 'gray',
                        default => 'gray',
                    }),
                TextEntry::make('user.name')
                    ->label('Owner'),
                TextEntry::make('user.email')
                    ->label('Owner email'),
                TextEntry::make('category.name')
                    ->label('Category'),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('goal_amount')
                    ->money('PHP'),
                TextEntry::make('raised_amount')
                    ->money('PHP'),
                ImageEntry::make('cover_image')
                    ->disk('public')
                    ->columnSpanFull(),
                TextEntry::make('thank_you_message')
                    ->columnSpanFull(),
                TextEntry::make('starts_at')
                    ->date(),
                TextEntry::make('ends_at')
                    ->date(),
                TextEntry::make('submitted_at')
                    ->dateTime(),
                TextEntry::make('reviewer.name')
                    ->label('Reviewed by'),
                TextEntry::make('reviewed_at')
                    ->dateTime(),
                TextEntry::make('rejection_reason')
                    ->columnSpanFull()
                    ->visible(fn (Campaign $record): bool => $record->status === Campaign::STATUS_REJECTED),
                RepeatableEntry::make('media')
                    ->label('Gallery')
                    ->schema([
                        ImageEntry::make('path')
                            ->disk('public'),
                    ])
                    ->columnSpanFull(),
                RepeatableEntry::make('documents')
                    ->label('Submitted documents')
                    ->schema([
                        TextEntry::make('documentType.name')
                            ->label('Document type'),
                        TextEntry::make('documentType.is_required')
                            ->label('Required')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('original_name')
                            ->label('File')
                            ->url(fn ($record): string => asset('storage/'.$record->path))
                            ->openUrlInNewTab(),
                        TextEntry::make('created_at')
                            ->label('Uploaded')
                            ->dateTime(),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
                TextEntry::make('missing_documents')
                    ->label('Missing required documents')
                    ->state(function (Campaign $record): string {
                        $missing = $record->missingRequiredDocumentTypes();

                        if ($missing->isEmpty()) {
                            return 'None';
                        }

                        return $missing->pluck('name')->join(', ');
                    })
                    ->color(fn (Campaign $record): string => $record->missingRequiredDocumentTypes()->isEmpty() ? 'success' : 'danger')
                    ->columnSpanFull(),
            ]);
    }
}
