<?php

namespace App\Http\Controllers\Api;

use App\Mail\CodigoVerificacionHuespedMail;
use App\Http\Controllers\Controller;
use App\Models\Huesped;
use App\Models\Role;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 4;

    private const LOGIN_DECAY_SECONDS = 900;

    private const LOGIN_CODE_TTL_MINUTES = 15;

    public function register(Request $request)
    {
        $request->merge([
            'correo_electronico' => $this->normalizeEmail($request->input('correo_electronico')),
        ]);

        $validator = Validator::make($request->all(), [
            'nombres' => ['required', 'string', 'max:60', 'regex:/^[\pL\pM]+(?:\s+[\pL\pM]+)*$/u'],
            'apellido_paterno' => ['required', 'string', 'max:60', 'regex:/^[\pL\pM]+(?:\s+[\pL\pM]+)*$/u'],
            'apellido_materno' => ['nullable', 'string', 'max:60', 'regex:/^[\pL\pM]+(?:\s+[\pL\pM]+)*$/u'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:' . CarbonImmutable::now()->subYears(21)->toDateString()],
            'tipo_documento' => ['required', 'string', 'max:30'],
            'numero_documento' => ['required', 'string', 'max:30', Rule::unique('huespedes', 'numero_documento')],
            'telefono' => ['required', 'string', 'max:15', 'regex:/^\+?[0-9]+$/'],
            'correo_electronico' => [
                'required',
                'email',
                Rule::unique('huespedes', 'correo_electronico'),
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Revisa los datos ingresados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $user = DB::transaction(function () use ($data): User {
            $rolHuesped = Role::query()
                ->where('nombre', 'HUESPED')
                ->first();

            if (! $rolHuesped) {
                throw ValidationException::withMessages([
                    'correo_electronico' => 'No se pudo completar el registro.',
                ]);
            }

            $nombreCompleto = trim(
                $data['nombres'] . ' ' .
                $data['apellido_paterno'] . ' ' .
                ($data['apellido_materno'] ?? '')
            );

            $user = User::create([
                'role_id' => $rolHuesped->id,
                'nombres' => $data['nombres'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?? null,
                'name' => $nombreCompleto,
                'email' => $data['correo_electronico'],
                'password' => Hash::make($data['password']),
                'estado' => true,
            ]);

            Huesped::create([
                'user_id' => $user->id,
                'nombres' => $data['nombres'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?? null,
                'tipo_documento' => $data['tipo_documento'],
                'numero_documento' => $data['numero_documento'],
                'telefono' => $data['telefono'],
                'correo_electronico' => $data['correo_electronico'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'estado' => true,
            ]);

            return $user;
        });

        $this->sendLoginCode($user);

        return response()->json([
            'success' => true,
            'message' => 'Registro realizado correctamente. Se envió un código de verificación a tu correo.',
            'requires_verification' => true,
            'email' => $user->email,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->merge([
            'email' => $this->normalizeEmail($request->input('email')),
        ]);

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

        $user = User::query()
            ->with(['role', 'huesped'])
            ->whereRaw('LOWER(email) = ?', [$credentials['email']])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
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

        if (! $user->estado) {
            return response()->json([
                'message' => 'Usuario inactivo.',
            ], 403);
        }

        if (! $user->role || $user->role->nombre !== 'HUESPED') {
            return response()->json([
                'message' => 'Este usuario no tiene acceso al panel huésped.',
            ], 403);
        }

        $huesped = $user->huesped;

        if (! $huesped) {
            return response()->json([
                'message' => 'Este usuario no tiene un perfil de huésped vinculado.',
            ], 403);
        }

        return response()->json([
            'token' => $user->createToken('panel-huesped')->plainTextToken,
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

    public function verifyLoginCode(Request $request)
    {
        $request->merge([
            'email' => $this->normalizeEmail($request->input('email')),
            'code' => trim((string) $request->input('code')),
        ]);

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Revisa los datos ingresados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $email = $data['email'];
        $code = $data['code'];
        $payload = Cache::get($this->loginCodeCacheKey($email));

        if (! is_array($payload)) {
            return response()->json([
                'message' => 'El código expiró o no existe.',
            ], 422);
        }

        if (! Hash::check($code, $payload['code_hash'] ?? '')) {
            return response()->json([
                'message' => 'El código ingresado no es válido.',
            ], 422);
        }

        $user = User::query()
            ->with(['role', 'huesped'])
            ->where('email', $email)
            ->first();

        if (! $user) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        if (! $user->estado) {
            return response()->json([
                'message' => 'Usuario inactivo.',
            ], 403);
        }

        if ($user->role?->nombre !== 'HUESPED') {
            return response()->json([
                'message' => 'Usuario no autorizado.',
            ], 403);
        }

        $huesped = $user->huesped;

        if (! $huesped) {
            return response()->json([
                'message' => 'Perfil de huésped no encontrado.',
            ], 404);
        }

        Cache::forget($this->loginCodeCacheKey($email));

        $token = $user->createToken('panel-huesped')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Verificación correcta.',
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

    private function sendLoginCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);
        $email = $this->normalizeEmail($user->email);

        Cache::put(
            $this->loginCodeCacheKey($email),
            ['code_hash' => Hash::make($code)],
            now()->addMinutes(self::LOGIN_CODE_TTL_MINUTES)
        );

        try {
            Mail::to($email)->send(new CodigoVerificacionHuespedMail(
                user: $user,
                code: $code,
                vigenciaMinutos: self::LOGIN_CODE_TTL_MINUTES,
            ));
        } catch (\Throwable $e) {
            Cache::forget($this->loginCodeCacheKey($email));

            throw $e;
        }
    }

    private function loginCodeCacheKey(string $email): string
    {
        return 'huesped_login_code:' . $this->normalizeEmail($email);
    }

    private function normalizeEmail(mixed $email): string
    {
        return strtolower(trim((string) $email));
    }
}
