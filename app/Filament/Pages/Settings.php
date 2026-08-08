<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasMaxWidth;
use Filament\Pages\Concerns\HasTopbar;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Request;

/**
 * @property-read Schema $form
 */
class Settings extends Page
{
    use CanUseDatabaseTransactions;
    use HasMaxWidth;
    use HasTopbar;

    protected static ?string $title = 'Site Settings';

    protected static ?string $slug = 'settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getLabel(): string
    {
        return 'Site Settings';
    }

    public static function canAccess(): bool
    {
        $admin = Filament::auth()->user();

        return $admin instanceof Admin && $admin->canAccessModule('settings');
    }

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->form->fill([
            'notification_email' => $settings->notification_email,
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = SiteSetting::current();
        $previousEmail = $settings->notification_email;

        /** @var Admin $admin */
        $admin = Filament::auth()->user();

        $settings->update([
            'notification_email' => $data['notification_email'],
            'updated_by' => $admin->id,
        ]);

        ActivityLog::query()->create([
            'admin_id' => $admin->id,
            'action' => 'settings.updated',
            'model' => 'SiteSetting',
            'model_id' => $settings->id,
            'changes' => [
                'notification_email' => [
                    'from' => $previousEmail,
                    'to' => $data['notification_email'],
                ],
            ],
            'ip' => Request::ip(),
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel(! static::isSimple())
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Notifications')
                    ->description('Configure sitewide notification recipients.')
                    ->schema([
                        Group::make([
                            TextInput::make('notification_email')
                                ->label('Notification email')
                                ->email()
                                ->required()
                                ->helperText('Campaign submission alerts are sent to this address.'),
                        ])
                            ->maxWidth(Width::Large),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment($this->getFormActionsAlignment())
                            ->fullWidth($this->hasFullWidthFormActions()),
                    ]),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save settings')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::Start;
    }

    public function getTitle(): string|Htmlable
    {
        return static::getLabel();
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'settings';
    }

    public static function isSimple(): bool
    {
        return false;
    }
}
