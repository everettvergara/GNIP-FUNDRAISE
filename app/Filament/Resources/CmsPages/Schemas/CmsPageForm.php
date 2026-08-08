<?php

namespace App\Filament\Resources\CmsPages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class CmsPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    TextInput::make('title')
                        ->required(),
                    TextInput::make('slug')
                        ->required(),
                    Textarea::make('body')
                        ->default(null)
                        ->columnSpanFull(),
                    TextInput::make('meta_title')
                        ->default(null),
                    Textarea::make('meta_description')
                        ->default(null)
                        ->columnSpanFull(),
                    Toggle::make('is_published')
                        ->required(),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }
}
