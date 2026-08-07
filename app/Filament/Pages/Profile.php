<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class Profile extends EditProfile
{
    protected static ?string $title = 'Admin Profile';

    protected static bool $shouldRegisterNavigation = false;

    public static function getLabel(): string
    {
        return 'Profile';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getAvatarFormComponent(),
            ]);
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar')
            ->label('Avatar')
            ->image()
            ->directory('admin-avatars')
            ->visibility('public');
    }
}
