<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('logo')
                        ->default(null),
                    TextInput::make('url')
                        ->url()
                        ->default(null),
                    TextInput::make('sort_order')
                        ->required()
                        ->numeric()
                        ->default(0),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }
}
