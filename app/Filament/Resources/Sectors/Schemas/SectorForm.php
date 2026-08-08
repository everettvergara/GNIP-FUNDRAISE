<?php

namespace App\Filament\Resources\Sectors\Schemas;

use App\Models\Sector;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class SectorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('slug')
                        ->required(),
                    Select::make('category')
                        ->options([
                            Sector::CATEGORY_QUALITY_OF_LIFE => Sector::CATEGORY_QUALITY_OF_LIFE,
                            Sector::CATEGORY_REDUCE_INEQUALITY => Sector::CATEGORY_REDUCE_INEQUALITY,
                        ])
                        ->required(),
                    Textarea::make('description')
                        ->required()
                        ->columnSpanFull(),
                    FileUpload::make('image')
                        ->image(),
                    TextInput::make('sort_order')
                        ->required()
                        ->numeric()
                        ->default(0),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }
}
