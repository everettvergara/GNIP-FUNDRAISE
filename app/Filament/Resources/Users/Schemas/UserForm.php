<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    TextInput::make('first_name')
                        ->required(),
                    TextInput::make('last_name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                    Select::make('role_id')
                        ->label('Role')
                        ->relationship(
                            name: 'role',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->where('audience', Role::AUDIENCE_CAMPAIGN_USER),
                        )
                        ->default(fn (): ?int => Role::defaultCampaignUser()->id)
                        ->required()
                        ->searchable()
                        ->preload(),
                    DateTimePicker::make('email_verified_at'),
                    TextInput::make('password')
                        ->password()
                        ->required(fn (string $operation): bool => $operation === 'create'),
                    FileUpload::make('avatar')
                        ->label('Avatar')
                        ->image()
                        ->directory('user-avatars')
                        ->visibility('public'),
                    Textarea::make('about_me')
                        ->rows(4)
                        ->columnSpanFull(),
                    TextInput::make('organization')
                        ->maxLength(255),
                    TextInput::make('position')
                        ->maxLength(255),
                    Toggle::make('is_profile_public')
                        ->label('Public profile')
                        ->default(true),
                    Toggle::make('is_active')
                        ->required(),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }
}
