<?php

namespace App\Filament\Resources\CampaignDocumentTypes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class CampaignDocumentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->columnSpanFull(),
                    Toggle::make('is_required')
                        ->label('Required for approval')
                        ->default(false),
                    Toggle::make('is_active')
                        ->default(true),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }
}
