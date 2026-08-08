<?php

namespace App\Filament\Resources\Admins\Schemas;

use App\Models\Role;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->label(fn (string $operation): string => $operation === 'create' ? 'Password' : 'New password')
                        ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Leave blank to keep the current password.' : null),
                    Select::make('role_id')
                        ->label('Role')
                        ->relationship(
                            name: 'role',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->where('audience', Role::AUDIENCE_ADMIN),
                        )
                        ->default(fn (): ?int => Role::defaultAdmin()->id)
                        ->required()
                        ->searchable()
                        ->preload(),
                    FileUpload::make('avatar')
                        ->label('Avatar')
                        ->avatar()
                        ->disk('public')
                        ->directory('admin-avatars')
                        ->visibility('public'),
                    Toggle::make('is_active')
                        ->required(),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }
}
