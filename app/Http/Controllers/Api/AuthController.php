<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

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
}