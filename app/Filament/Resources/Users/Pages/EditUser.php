<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Role;
use App\Support\LogSistema;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $rolAnterior = null;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver'),

            DeleteAction::make()
                ->label('Dar de baja')
                ->modalHeading('Dar de baja usuario administrativo')
                ->modalDescription('El usuario será enviado a la papelera y podrá recuperarse posteriormente.')
                ->modalSubmitActionLabel('Sí, dar de baja')
                ->successNotificationTitle('Usuario dado de baja correctamente')
                ->visible(fn (): bool => UserResource::puedeGestionarUsuario($this->record, permitirPropio: false))
                ->after(function (): void {
                    LogSistema::registrar(
                        'ELIMINAR',
                        'Usuarios',
                        'Usuario administrativo dado de baja: ' . $this->record->name . ' (' . $this->record->email . ').'
                    );
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Filament::auth()->user();
        $record = $this->record;
        $role = Role::find($data['role_id'] ?? null);

        if (! $user?->role || ! $record->role || ! $role) {
            throw ValidationException::withMessages([
                'role_id' => 'No tienes permisos para modificar este usuario.',
            ]);
        }

        $this->rolAnterior = $record->role->nombre;

        // No se permite modificar la propia cuenta desde Usuarios administrativos.
        if ((int) $user->id === (int) $record->id) {
            throw ValidationException::withMessages([
                'email' => 'No puedes modificar tu propia cuenta desde Usuarios administrativos.',
            ]);
        }

        if (! UserResource::puedeGestionarUsuario($record, permitirPropio: false)) {
            throw ValidationException::withMessages([
                'role_id' => 'No puedes modificar este usuario.',
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

        // La contraseña no se edita desde Filament.
        unset($data['password']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->loadMissing('role');

        LogSistema::registrar(
            'EDITAR',
            'Usuarios',
            'Usuario administrativo editado: ' . $this->record->name . ' (' . $this->record->email . ').'
        );

        $rolActual = $this->record->role?->nombre;

        if ($this->rolAnterior && $rolActual && $this->rolAnterior !== $rolActual) {
            LogSistema::registrar(
                'CAMBIAR_ROL',
                'Usuarios',
                'Rol cambiado para ' . $this->record->name . ': ' . $this->rolAnterior . ' -> ' . $rolActual . '.'
            );
        }
    }
}
