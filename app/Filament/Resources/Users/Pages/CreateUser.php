<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

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

        $data['password'] = Hash::make(Str::random(32));
        $data['estado'] = $data['estado'] ?? true;
        $data['email_verified_at'] = now();

        return $data;
    }
}