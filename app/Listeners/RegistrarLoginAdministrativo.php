<?php

namespace App\Listeners;

use App\Models\User;
use App\Support\LogSistema;
use Illuminate\Auth\Events\Login;
use Throwable;

class RegistrarLoginAdministrativo
{
    private const ROLES_ADMINISTRATIVOS = [
        'SUPER ADMIN',
        'ADMIN',
        'RECEPCIONISTA',
        'CHEF',
    ];

    public function handle(Login $event): void
    {
        try {
            $user = $event->user;

            if (! $user instanceof User) {
                return;
            }

            if (! request()->is('admin') && ! request()->is('admin/*')) {
                return;
            }

            $user->loadMissing('role');

            if (! $user->role || ! in_array($user->role->nombre, self::ROLES_ADMINISTRATIVOS, true)) {
                return;
            }

            LogSistema::registrar(
                'LOGIN',
                'Autenticación',
                'Usuario inició sesión en el panel administrativo.',
                $user
            );
        } catch (Throwable) {
            // El log no debe interrumpir el inicio de sesion administrativo.
        }
    }
}
