<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Campaign;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Grid::make(3)
                        ->columnSpanFull()
                        ->schema([
                            Group::make(self::fields())
                                ->columnSpan(fn (string $operation): int => $operation === 'edit' ? 2 : 3),
                            Placeholder::make('campaign_history')
                                ->hiddenLabel()
                                ->content(function (?Campaign $record): HtmlString {
                                    if (! $record) {
                                        return new HtmlString('');
                                    }

                                    $record->loadMissing(['events.user', 'events.admin']);

                                    return new HtmlString(
                                        view('filament.resources.campaigns.campaign-history', ['campaign' => $record])->render()
                                    );
                                })
                                ->columnSpan(1)
                                ->visibleOn(['edit']),
                        ]),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }

    /**
     * @return array<int, Component>
     */
    private static function fields(): array
    {
        return [
            Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),
            Select::make('category_id')
                ->relationship('category', 'name')
                ->default(null),
            TextInput::make('title')
                ->required(),
            TextInput::make('slug')
                ->required(),
            Textarea::make('description')
                ->required()
                ->columnSpanFull(),
            TextInput::make('goal_amount')
                ->required()
                ->numeric(),
            TextInput::make('raised_amount')
                ->numeric()
                ->disabled()
                ->dehydrated(false)
                ->helperText('Auto-calculated from confirmed donations.'),
            FileUpload::make('cover_image')
                ->image()
                ->directory('campaigns'),
            Repeater::make('media')
                ->relationship()
                ->label('Gallery')
                ->schema([
                    FileUpload::make('path')
                        ->image()
                        ->directory('campaigns/gallery')
                        ->required(),
                    Hidden::make('type')
                        ->default('image'),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ])
                ->defaultItems(0)
                ->reorderable()
                ->orderColumn('sort_order')
                ->columnSpanFull(),
            Textarea::make('thank_you_message')
                ->default(null)
                ->columnSpanFull(),
            Select::make('status')
                ->options(Campaign::STATUSES)
                ->required()
                ->default(Campaign::STATUS_DRAFT)
                ->disableOptionWhen(fn (string $value): bool => $value === Campaign::STATUS_ACTIVE)
                ->helperText('Use Approve & Publish to make a campaign active.'),
            Toggle::make('is_featured')
                ->required(),
            DatePicker::make('starts_at'),
            DatePicker::make('ends_at'),
            Textarea::make('rejection_reason')
                ->columnSpanFull()
                ->visible(fn (?Campaign $record): bool => $record?->status === Campaign::STATUS_REJECTED),
        ];
    }
}
