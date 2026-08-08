<?php

namespace App\Filament\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
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
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use LogicException;
use Throwable;

/**
 * @property-read Schema $form
 */
class ChangePassword extends Page
{
    use CanUseDatabaseTransactions;
    use HasMaxWidth;
    use HasTopbar;
    use WithRateLimiting;

    protected static ?string $title = 'Change Password';

    protected static ?string $slug = 'change-password';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getLabel(): string
    {
        return 'Change Password';
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getUser(): Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new LogicException('The authenticated user object must be an Eloquent model.');
        }

        return $user;
    }

    public function save(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title('Too many attempts')
                ->body('Please try again in '.$exception->secondsUntilAvailable.' seconds.')
                ->danger()
                ->send();

            return;
        }

        $rateLimitingKey = 'filament-change-password:'.Filament::auth()->id();

        if (RateLimiter::tooManyAttempts($rateLimitingKey, maxAttempts: 5)) {
            Notification::make()
                ->title('Too many attempts')
                ->danger()
                ->send();

            return;
        }

        RateLimiter::hit($rateLimitingKey);

        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();

            $this->getUser()->update([
                'password' => $data['password'],
            ]);

            $this->getUser()->refresh();

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction()
                ? $this->rollBackDatabaseTransaction()
                : $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_'.Filament::getAuthGuard() => $this->getUser()->getAuthPassword(),
            ]);
        }

        $this->data = [];

        Notification::make()
            ->title('Password updated')
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
                Group::make([
                    TextInput::make('current_password')
                        ->label('Current password')
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->required()
                        ->currentPassword(guard: Filament::getAuthGuard())
                        ->dehydrated(false),
                    TextInput::make('password')
                        ->label('New password')
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->rule(Password::default())
                        ->required()
                        ->same('passwordConfirmation')
                        ->validationAttribute('password'),
                    TextInput::make('passwordConfirmation')
                        ->label('Confirm new password')
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->required()
                        ->dehydrated(false),
                ])
                    ->maxWidth(Width::Large),
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
                ->label('Save password')
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
        return 'change-password';
    }

    public static function isSimple(): bool
    {
        return false;
    }
}
