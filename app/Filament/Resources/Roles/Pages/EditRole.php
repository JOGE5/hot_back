<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Filament::auth()->user();
        $record = $this->record;

        if (! $user?->role) {
            throw ValidationException::withMessages([
                'estado' => 'No tienes permisos para modificar roles.',
            ]);
        }

        // Evita que se cambie el nombre aunque alguien intente forzarlo.
        $data['nombre'] = $record->nombre;

        // Solo roles administrativos pueden editarse desde esta pestaña.
        if (! in_array($record->nombre, RoleResource::ROLES_ADMINISTRATIVOS, true)) {
            throw ValidationException::withMessages([
                'nombre' => 'Este rol no puede modificarse desde el panel administrativo.',
            ]);
        }

        // ADMIN no puede modificar SUPER ADMIN.
        if ($user->role->nombre === 'ADMIN' && $record->nombre === 'SUPER ADMIN') {
            throw ValidationException::withMessages([
                'nombre' => 'Un ADMIN no puede modificar el rol SUPER ADMIN.',
            ]);
        }

        // Nadie puede desactivar su propio rol.
        if ((int) $user->role_id === (int) $record->id && isset($data['estado']) && $data['estado'] === false) {
            throw ValidationException::withMessages([
                'estado' => 'No puedes desactivar tu propio rol.',
            ]);
        }

        return $data;
    }
}