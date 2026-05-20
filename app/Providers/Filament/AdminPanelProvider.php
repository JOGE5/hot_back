<?php

namespace App\Providers\Filament;

use App\Filament\Pages\CambiarPassword;
use App\Filament\Pages\Dashboard;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\HtmlString;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
       return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->globalSearch(false)
            ->colors([
                'primary' => Color::Green,
            ])
            ->userMenuItems([
                Action::make('cambiar_password')
                    ->label('Cambiar contraseña')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->modalHeading('Cambiar contraseña')
                    ->modalDescription('Actualiza tu contraseña de acceso administrativo.')
                    ->modalContent(fn (): HtmlString => CambiarPassword::modalMarker())
                    ->modalWidth('md')
                    ->modalAlignment(Alignment::Center)
                    ->modalSubmitActionLabel('Guardar contraseña')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalFooterActionsAlignment(Alignment::Center)
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false)
                    ->form(fn (): array => CambiarPassword::schemaCambioConPasswordActual())
                    ->action(fn (array $data) => CambiarPassword::cambiarConPasswordActual($data))
                    ->extraModalFooterActions([
                        Action::make('olvide_password')
                            ->label('¿Olvidaste tu contraseña?')
                            ->icon('heroicon-o-envelope')
                            ->color('gray')
                            ->modalHeading('Código de verificación')
                            ->modalDescription('Enviaremos un código de 6 dígitos a tu correo. Tiene una vigencia de 15 minutos.')
                            ->modalContent(fn (): HtmlString => CambiarPassword::modalMarker())
                            ->modalWidth('md')
                            ->modalAlignment(Alignment::Center)
                            ->modalSubmitActionLabel('Cambiar contraseña')
                            ->modalCancelActionLabel('Cancelar')
                            ->modalFooterActionsAlignment(Alignment::Center)
                            ->closeModalByClickingAway(false)
                            ->closeModalByEscaping(false)
                            ->mountUsing(function (Action $action, ?Schema $schema): void {
                                CambiarPassword::enviarCodigoCambioPassword();

                                $schema?->fill();
                            })
                            ->steps(fn (): array => CambiarPassword::stepsCambioConCodigo())
                            ->action(fn (array $data) => CambiarPassword::cambiarConCodigo($data)),
                    ])
                    ->visible(function (): bool {
                        return CambiarPassword::puedeUsarCambioPassword();
                    }),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): HtmlString => new HtmlString(
                    '<link rel="stylesheet" href="' . asset('css/filament/admin-dashboard.css') . '?v=1">' .
                    CambiarPassword::modalStyles()
                ),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
