<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->required(),
                TextInput::make('action')
                    ->required(),
                TextInput::make('model')
                    ->default(null),
                TextInput::make('model_id')
                    ->numeric()
                    ->default(null),
                Textarea::make('changes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('ip')
                    ->default(null),
            ]);
    }
}
