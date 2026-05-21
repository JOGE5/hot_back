<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Bienvenido';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Ingresa tus credenciales para acceder al panel administrativo.';
    }
}
