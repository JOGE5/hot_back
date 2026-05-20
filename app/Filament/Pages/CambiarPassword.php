<?php

namespace App\Filament\Pages;

use App\Mail\CodigoCambioPasswordMail;
use App\Models\User;
use App\Support\LogSistema;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Throwable;
use UnitEnum;

class CambiarPassword extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationLabel = 'Cambiar contraseña';

    protected static string|UnitEnum|null $navigationGroup = 'Cuenta';

    protected static ?string $title = 'Cambiar contraseña';

    protected static ?int $navigationSort = 90;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return static::puedeUsarCambioPassword();
    }

    public static function puedeUsarCambioPassword(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPCIONISTA',
            'CHEF',
        ], true);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(static::schemaCambioConPasswordActual());
    }

    public static function schemaCambioConPasswordActual(): array
    {
        return [
            TextInput::make('password_actual')
                ->label('Contraseña actual')
                ->password()
                ->revealable()
                ->autocomplete('current-password')
                ->required()
                ->validationAttribute('contraseña actual')
                ->validationMessages([
                    'required' => 'La contraseña actual es obligatoria.',
                ]),

            TextInput::make('password')
                ->label('Nueva contraseña')
                ->password()
                ->revealable()
                ->autocomplete('new-password')
                ->required()
                ->minLength(8)
                ->same('password_confirmation')
                ->validationAttribute('nueva contraseña')
                ->validationMessages([
                    'required' => 'La nueva contraseña es obligatoria.',
                    'min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
                    'same' => 'La confirmación debe coincidir con la nueva contraseña.',
                ]),

            TextInput::make('password_confirmation')
                ->label('Confirmar nueva contraseña')
                ->password()
                ->revealable()
                ->autocomplete('new-password')
                ->required()
                ->validationAttribute('confirmación de contraseña')
                ->validationMessages([
                    'required' => 'La confirmación de contraseña es obligatoria.',
                ]),
        ];
    }

    public static function stepsCambioConCodigo(): array
    {
        return [
            Step::make('Verificación')
                ->description('Ingresa el código enviado a tu correo.')
                ->schema([
                    TextInput::make('codigo')
                        ->label('Código de verificación')
                        ->required()
                        ->length(6)
                        ->numeric()
                        ->validationAttribute('código de verificación')
                        ->rule(static::reglaCodigoVerificacion())
                        ->validationMessages([
                            'required' => 'El código de verificación es obligatorio.',
                            'digits' => 'El código de verificación debe tener 6 dígitos.',
                            'numeric' => 'El código de verificación debe contener solo números.',
                        ]),
                ]),

            Step::make('Nueva contraseña')
                ->description('Define tu nueva contraseña.')
                ->schema([
                    TextInput::make('password')
                        ->label('Nueva contraseña')
                        ->password()
                        ->revealable()
                        ->autocomplete('new-password')
                        ->required()
                        ->minLength(8)
                        ->same('password_confirmation')
                        ->validationAttribute('nueva contraseña')
                        ->validationMessages([
                            'required' => 'La nueva contraseña es obligatoria.',
                            'min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
                            'same' => 'La confirmación debe coincidir con la nueva contraseña.',
                        ]),

                    TextInput::make('password_confirmation')
                        ->label('Confirmar nueva contraseña')
                        ->password()
                        ->revealable()
                        ->autocomplete('new-password')
                        ->required()
                        ->validationAttribute('confirmación de contraseña')
                        ->validationMessages([
                            'required' => 'La confirmación de contraseña es obligatoria.',
                        ]),
                ]),
        ];
    }

    public static function cambiarConPasswordActual(array $data, string $errorPrefix = ''): void
    {
        $user = static::usuarioAutenticado($errorPrefix);

        if (! Hash::check($data['password_actual'] ?? '', $user->password)) {
            throw ValidationException::withMessages([
                $errorPrefix . 'password_actual' => 'La contraseña actual no es correcta.',
            ]);
        }

        static::actualizarPassword($user, $data['password']);

        LogSistema::registrar(
            'CAMBIAR_PASSWORD',
            'Autenticación',
            'Usuario cambió su propia contraseña.'
        );

        Notification::make()
            ->success()
            ->title('Contraseña actualizada correctamente.')
            ->send();
    }

    public static function enviarCodigoCambioPassword(): void
    {
        $user = static::usuarioAutenticado();
        $codigo = (string) random_int(100000, 999999);
        $expiraEn = now()->addMinutes(15);

        request()->session()->put(static::sessionKeyCodigo($user), [
            'hash' => Hash::make($codigo),
            'expires_at' => $expiraEn->timestamp,
        ]);

        try {
            Mail::to($user->email)->send(new CodigoCambioPasswordMail(
                user: $user,
                codigo: $codigo,
                vigenciaMinutos: 15,
            ));
        } catch (Throwable $e) {
            request()->session()->forget(static::sessionKeyCodigo($user));

            throw $e;
        }

        Notification::make()
            ->success()
            ->title('Código enviado')
            ->body('Revisa tu correo e ingresa el código de 6 dígitos.')
            ->send();
    }

    public static function cambiarConCodigo(array $data, string $errorPrefix = ''): void
    {
        $user = static::usuarioAutenticado($errorPrefix);

        if (! static::codigoValido($user, (string) ($data['codigo'] ?? ''))) {
            throw ValidationException::withMessages([
                $errorPrefix . 'codigo' => 'El código es incorrecto o está vencido.',
            ]);
        }

        static::actualizarPassword($user, $data['password']);
        request()->session()->forget(static::sessionKeyCodigo($user));

        LogSistema::registrar(
            'CAMBIAR_PASSWORD_CODIGO',
            'Autenticación',
            'Usuario cambió su contraseña usando código de verificación.'
        );

        Notification::make()
            ->success()
            ->title('Contraseña actualizada correctamente.')
            ->send();
    }

    public static function modalMarker(): HtmlString
    {
        return new HtmlString('<div class="hot-password-modal-marker" aria-hidden="true"></div>');
    }

    public static function modalStyles(): string
    {
        return <<<'HTML'
<style>
    .fi-modal-window:has(.hot-password-modal-marker),
    .hot-password-page-card {
        background: #1e1e1e !important;
        border: 1px solid #3d3c3c !important;
        border-radius: 10px !important;
        color: #ffffff !important;
        text-align: center;
    }

    .fi-modal-window:has(.hot-password-modal-marker) .fi-modal-heading,
    .fi-modal-window:has(.hot-password-modal-marker) .fi-modal-description,
    .fi-modal-window:has(.hot-password-modal-marker) .fi-fo-field-wrp-label span,
    .hot-password-page-card .fi-fo-field-wrp-label span {
        color: #ffffff !important;
    }

    .fi-modal-window:has(.hot-password-modal-marker) .fi-input-wrp,
    .hot-password-page-card .fi-input-wrp {
        background: transparent !important;
        border: 2px solid orange !important;
        border-radius: 5px !important;
        box-shadow: none !important;
        transition: .5s;
    }

    .fi-modal-window:has(.hot-password-modal-marker) input,
    .hot-password-page-card input {
        min-height: 44px;
        color: #ffffff !important;
        background: transparent !important;
        font-size: 16px !important;
    }

    .fi-modal-window:has(.hot-password-modal-marker) input:focus,
    .hot-password-page-card input:focus {
        color: #111827 !important;
        background: orange !important;
    }

    .fi-modal-window:has(.hot-password-modal-marker) .fi-btn,
    .hot-password-page-card .fi-btn {
        border: none !important;
        border-radius: 5px !important;
    }

    .fi-modal-window:has(.hot-password-modal-marker) .fi-btn-color-primary,
    .fi-modal-window:has(.hot-password-modal-marker) .fi-btn-color-warning,
    .hot-password-page-card .fi-btn-color-primary,
    .hot-password-page-card .fi-btn-color-warning {
        color: #ffffff !important;
        background: orange !important;
        box-shadow: none !important;
    }

    .fi-modal-window:has(.hot-password-modal-marker) .fi-btn-color-gray {
        color: #ffffff !important;
        background: #3d3c3c !important;
    }

    .fi-modal-window:has(.hot-password-modal-marker) .fi-wizard-header,
    .fi-modal-window:has(.hot-password-modal-marker) .fi-wizard-step {
        color: #ffffff !important;
    }
</style>
HTML;
    }

    public function save(): void
    {
        $data = $this->form->getState();

        static::cambiarConPasswordActual($data, 'data.');

        $this->form->fill();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Html::make(static::modalStyles()),
                Form::make([
                    Html::make(static::modalMarker()),
                    Section::make()
                        ->schema([
                            EmbeddedSchema::make('form'),
                        ])
                        ->extraAttributes([
                            'class' => 'hot-password-page-card',
                            'style' => 'max-width: 520px; margin: 0 auto; padding: 20px;',
                        ]),
                ])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Guardar contraseña')
                                ->color('warning')
                                ->submit('save'),
                        ])
                            ->alignment(Alignment::Center),
                    ]),
            ]);
    }

    private static function usuarioAutenticado(string $errorPrefix = ''): User
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            throw ValidationException::withMessages([
                $errorPrefix . 'password_actual' => 'No se pudo identificar al usuario autenticado.',
            ]);
        }

        return $user;
    }

    private static function actualizarPassword(User $user, string $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();

        request()->session()->put([
            'password_hash_' . Filament::getAuthGuard() => $user->password,
        ]);
    }

    private static function reglaCodigoVerificacion(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            try {
                $user = static::usuarioAutenticado();
            } catch (Throwable) {
                $fail('No se pudo identificar al usuario autenticado.');

                return;
            }

            if (! static::codigoValido($user, (string) $value)) {
                $fail('El código es incorrecto o está vencido.');
            }
        };
    }

    private static function codigoValido(User $user, string $codigo): bool
    {
        $payload = request()->session()->get(static::sessionKeyCodigo($user));

        if (! is_array($payload)) {
            return false;
        }

        if (($payload['expires_at'] ?? 0) < now()->timestamp) {
            request()->session()->forget(static::sessionKeyCodigo($user));

            return false;
        }

        return Hash::check($codigo, $payload['hash'] ?? '');
    }

    private static function sessionKeyCodigo(User $user): string
    {
        return 'admin-password-change-code:' . $user->id;
    }
}
