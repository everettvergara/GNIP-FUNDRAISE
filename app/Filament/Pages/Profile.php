<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

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
                Group::make([
                    $this->getAvatarFormComponent(),
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getContactFormComponent(),
                    $this->getAboutMeFormComponent(),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()->autofocus(false);
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar')
            ->label('Avatar')
            ->avatar()
            ->disk('public')
            ->directory('admin-avatars')
            ->visibility('public');
    }

    protected function getContactFormComponent(): Component
    {
        return TextInput::make('contact')
            ->label('Contact')
            ->tel()
            ->maxLength(255);
    }

    protected function getAboutMeFormComponent(): Component
    {
        return Textarea::make('about_me')
            ->label('About Me')
            ->rows(4);
    }
}
