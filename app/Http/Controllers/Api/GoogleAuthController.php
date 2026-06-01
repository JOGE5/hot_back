<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CodigoVerificacionHuespedMail;
use App\Models\Huesped;
use App\Models\Role;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    private const GOOGLE_REGISTER_TTL_MINUTES = 15;

    private const LOGIN_CODE_TTL_MINUTES = 15;

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();
        } catch (Throwable) {
            return $this->redirectToFrontend('/login', [
                'error' => 'No se pudo autenticar con Google.',
            ]);
        }

        $email = $this->normalizeEmail($googleUser->getEmail());

        if (! $email) {
            return $this->redirectToFrontend('/login', [
                'error' => 'Google no proporcionó un correo electrónico válido.',
            ]);
        }

        $user = User::query()
            ->with(['role', 'huesped'])
            ->where('email', $email)
            ->first();

        if ($user) {
            if (! $user->estado || $user->role?->nombre !== 'HUESPED' || ! $user->huesped) {
                return $this->redirectToFrontend('/login', [
                    'error' => 'El correo de Google ya pertenece a una cuenta no autorizada.',
                ]);
            }

            $this->sendLoginCode($user);

            return $this->redirectToFrontend('/verificar-codigo', [
                'email' => $email,
            ]);
        }

        if (Huesped::withTrashed()->whereRaw('LOWER(correo_electronico) = ?', [$email])->exists()) {
            return $this->redirectToFrontend('/login', [
                'error' => 'El correo de Google ya está registrado.',
            ]);
        }

        $token = Str::random(64);
        $names = $this->extractNames($googleUser);

        Cache::put(
            $this->googleRegisterCacheKey($token),
            [
                'email' => $email,
                'name' => $googleUser->getName() ?: trim($names['nombres'] . ' ' . $names['apellido_paterno']),
                'nombres' => $names['nombres'],
                'apellido_paterno' => $names['apellido_paterno'],
                'avatar' => $googleUser->getAvatar(),
            ],
            now()->addMinutes(self::GOOGLE_REGISTER_TTL_MINUTES)
        );

        return $this->redirectToFrontend('/register-google-complete', [
            'token' => $token,
        ]);
    }

    public function completeRegister(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token_temporal' => ['required', 'string'],
            'tipo_documento' => ['required', 'string', 'max:30'],
            'numero_documento' => ['required', 'string', 'max:30', Rule::unique('huespedes', 'numero_documento')],
            'telefono' => ['required', 'string', 'max:15', 'regex:/^\+?[0-9]+$/'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:' . CarbonImmutable::now()->subYears(21)->toDateString()],
            'apellido_materno' => ['nullable', 'string', 'max:60', 'regex:/^[\pL\pM]+(?:\s+[\pL\pM]+)*$/u'],
            'nacionalidad' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Revisa los datos ingresados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $token = $data['token_temporal'];
        $googleData = Cache::get($this->googleRegisterCacheKey($token));

        if (! is_array($googleData)) {
            return response()->json([
                'message' => 'La sesión de registro con Google expiró o no existe.',
            ], 422);
        }

        $email = $this->normalizeEmail($googleData['email'] ?? '');

        if (
            ! $email ||
            User::withTrashed()->whereRaw('LOWER(email) = ?', [$email])->exists() ||
            Huesped::withTrashed()->whereRaw('LOWER(correo_electronico) = ?', [$email])->exists()
        ) {
            Cache::forget($this->googleRegisterCacheKey($token));

            return response()->json([
                'message' => 'El correo de Google ya está registrado.',
            ], 422);
        }

        $user = DB::transaction(function () use ($data, $googleData, $email): User {
            $rolHuesped = Role::query()
                ->where('nombre', 'HUESPED')
                ->firstOrFail();

            $nombres = $googleData['nombres'];
            $apellidoPaterno = $googleData['apellido_paterno'];
            $apellidoMaterno = $data['apellido_materno'] ?? null;
            $nombreCompleto = trim($nombres . ' ' . $apellidoPaterno . ' ' . ($apellidoMaterno ?? ''));

            $user = User::create([
                'role_id' => $rolHuesped->id,
                'nombres' => $nombres,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'name' => $nombreCompleto,
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'estado' => true,
            ]);

            Huesped::create([
                'user_id' => $user->id,
                'nombres' => $nombres,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'tipo_documento' => $data['tipo_documento'],
                'numero_documento' => $data['numero_documento'],
                'telefono' => $data['telefono'],
                'correo_electronico' => $email,
                'nacionalidad' => $data['nacionalidad'] ?? 'Boliviana',
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'estado' => true,
            ]);

            return $user;
        });

        Cache::forget($this->googleRegisterCacheKey($token));
        $this->sendLoginCode($user);

        return response()->json([
            'success' => true,
            'message' => 'Registro con Google completado. Se envió un código de verificación a tu correo.',
            'requires_verification' => true,
            'email' => $email,
        ]);
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
        } catch (Throwable $e) {
            Cache::forget($this->loginCodeCacheKey($email));

            throw $e;
        }
    }

    private function extractNames($googleUser): array
    {
        $raw = $googleUser->user ?? [];
        $givenName = trim((string) ($raw['given_name'] ?? ''));
        $familyName = trim((string) ($raw['family_name'] ?? ''));
        $fullName = trim((string) ($googleUser->getName() ?? ''));

        if ($givenName && $familyName) {
            return [
                'nombres' => $givenName,
                'apellido_paterno' => $familyName,
            ];
        }

        $parts = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return [
            'nombres' => $givenName ?: ($parts[0] ?? 'Huésped'),
            'apellido_paterno' => $familyName ?: ($parts[1] ?? 'Google'),
        ];
    }

    private function redirectToFrontend(string $path, array $query = []): RedirectResponse
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', env('FRONTEND_URL', 'http://127.0.0.1:5173')), '/');
        $url = $frontendUrl . '/' . ltrim($path, '/');

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return redirect()->away($url);
    }

    private function googleRegisterCacheKey(string $token): string
    {
        return 'google_register:' . $token;
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
