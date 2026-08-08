<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Role;
use App\Support\ModuleAccess;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->alphaDash()
                        ->disabled(fn (?Role $record): bool => $record?->is_system ?? false),
                    Select::make('audience')
                        ->options([
                            Role::AUDIENCE_ADMIN => 'Admin',
                            Role::AUDIENCE_CAMPAIGN_USER => 'Campaign User',
                        ])
                        ->required()
                        ->live()
                        ->disabled(fn (string $operation): bool => $operation === 'edit'),
                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                    CheckboxList::make('modules')
                        ->label('Module access')
                        ->options(fn (Get $get): array => ModuleAccess::optionsForAudience($get('audience')))
                        ->columns(2)
                        ->bulkToggleable()
                        ->columnSpanFull()
                        ->disabled(fn (?Role $record): bool => $record?->slug === Role::SLUG_SUPER_ADMIN),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }
}
