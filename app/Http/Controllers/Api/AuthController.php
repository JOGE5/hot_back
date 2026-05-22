<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 4;

    private const LOGIN_DECAY_SECONDS = 900;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $rateLimitKey = $this->loginRateLimitKey($request, $credentials['email']);

        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_LOGIN_ATTEMPTS)) {
            return response()->json([
                'message' => 'Demasiados intentos fallidos. Intenta nuevamente en unos minutos.',
                'bloqueado' => true,
            ], 429);
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($rateLimitKey, self::LOGIN_DECAY_SECONDS);

            return response()->json([
                'message' => 'Correo o contraseña incorrectos.',
                'intentos_restantes' => max(
                    0,
                    self::MAX_LOGIN_ATTEMPTS - RateLimiter::attempts($rateLimitKey)
                ),
            ], 401);
        }

        RateLimiter::clear($rateLimitKey);

        $user = $request->user()->load('role');

        if (! $user->estado) {
            Auth::logout();

            return response()->json([
                'message' => 'Usuario inactivo.',
            ], 403);
        }

        if (! $user->role || $user->role->nombre !== 'HUESPED') {
            Auth::logout();

            return response()->json([
                'message' => 'Este usuario no tiene acceso al panel huésped.',
            ], 403);
        }

        $huesped = $user->huesped;

        if (! $huesped) {
            Auth::logout();

            return response()->json([
                'message' => 'Este usuario no tiene un perfil de huésped vinculado.',
            ], 403);
        }

        $token = $user->createToken('panel-huesped')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
                'rol' => $user->role->nombre,
            ],
            'huesped' => [
                'id' => $huesped->id,
                'nombres' => $huesped->nombres,
                'apellido_paterno' => $huesped->apellido_paterno,
                'apellido_materno' => $huesped->apellido_materno,
                'correo_electronico' => $huesped->correo_electronico,
                'numero_documento' => $huesped->numero_documento,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['role', 'huesped']);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    private function loginRateLimitKey(Request $request, string $email): string
    {
        return strtolower($email) . '|' . $request->ip();
    }
}
