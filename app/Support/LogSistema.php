<?php

namespace App\Support;

use App\Models\LogUser;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LogSistema
{
    public static function registrar(
        string $accion,
        string $modulo,
        ?string $descripcion = null,
        ?User $user = null
    ): void {
        try {
            $user ??= self::usuarioActual();

            LogUser::create([
                'user_id' => $user?->id,
                'rol' => $user?->role?->nombre,
                'accion' => $accion,
                'modulo' => $modulo,
                'descripcion' => $descripcion,
                'ip' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // La bitacora nunca debe interrumpir la accion principal.
        }
    }

    private static function usuarioActual(): ?User
    {
        try {
            $user = Filament::auth()->user();

            if ($user instanceof User) {
                return $user;
            }
        } catch (Throwable) {
            //
        }

        try {
            $user = Auth::user();

            return $user instanceof User ? $user : null;
        } catch (Throwable) {
            return null;
        }
    }
}
