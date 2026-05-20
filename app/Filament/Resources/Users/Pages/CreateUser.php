<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Mail\CredencialesUsuarioMail;
use App\Models\Role;
use App\Support\LogSistema;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Throwable;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $passwordTemporal = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();
        $role = Role::find($data['role_id'] ?? null);

        if (! $user?->role || ! $role) {
            throw ValidationException::withMessages([
                'role_id' => 'No tienes permisos para crear usuarios administrativos.',
            ]);
        }

        $rolesPermitidos = match ($user->role->nombre) {
            'SUPER ADMIN' => UserResource::ROLES_CREABLES_SUPER_ADMIN,
            'ADMIN' => UserResource::ROLES_CREABLES_ADMIN,
            default => [],
        };

        if (! in_array($role->nombre, $rolesPermitidos, true)) {
            throw ValidationException::withMessages([
                'role_id' => 'No puedes asignar este rol.',
            ]);
        }

        $data['name'] = trim(
            $data['nombres'] . ' ' .
            $data['apellido_paterno'] . ' ' .
            ($data['apellido_materno'] ?? '')
        );

        $this->passwordTemporal = Str::password(12);
        $data['password'] = Hash::make($this->passwordTemporal);
        $data['estado'] = $data['estado'] ?? true;
        $data['email_verified_at'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->loadMissing('role');

        $rol = $this->record->role?->nombre ?? 'Sin rol';

        LogSistema::registrar(
            'CREAR',
            'Usuarios',
            'Usuario administrativo creado: ' . $this->record->name . ' (' . $this->record->email . ') con rol ' . $rol . '.'
        );

        try {
            Mail::to($this->record->email)->send(new CredencialesUsuarioMail(
                user: $this->record,
                passwordTemporal: $this->passwordTemporal ?? '',
                urlAcceso: $this->resolverUrlAcceso($rol),
            ));

            LogSistema::registrar(
                'ENVIAR_CREDENCIALES',
                'Usuarios',
                'Correo de credenciales enviado a ' . $this->record->email . ' para el usuario ' . $this->record->name . '.'
            );
        } catch (Throwable $e) {
            LogSistema::registrar(
                'ERROR_ENVIO_CREDENCIALES',
                'Usuarios',
                'No se pudo enviar el correo de credenciales a ' . $this->record->email . '. Error: ' . $e::class . '.'
            );

            Notification::make()
                ->warning()
                ->title('Usuario creado, pero no se pudo enviar el correo de credenciales.')
                ->send();
        }
    }

    private function resolverUrlAcceso(string $rol): string
    {
        return $rol === 'HUESPED'
            ? 'http://127.0.0.1:5173/login'
            : 'http://127.0.0.1:8000/admin';
    }
}
