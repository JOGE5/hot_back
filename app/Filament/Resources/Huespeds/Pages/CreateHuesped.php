<?php

namespace App\Filament\Resources\Huespeds\Pages;

use App\Filament\Resources\Huespeds\HuespedResource;
use App\Mail\CredencialesUsuarioMail;
use App\Models\Huesped;
use App\Models\Role;
use App\Models\User;
use App\Support\LogSistema;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateHuesped extends CreateRecord
{
    protected static string $resource = HuespedResource::class;

    protected ?string $passwordTemporal = null;

    protected ?User $usuarioHuesped = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['correo_electronico'] = $this->normalizarCorreo($data['correo_electronico'] ?? null);

        if (($data['nacionalidad'] ?? '') === 'Otra') {
            $data['nacionalidad'] = $data['nacionalidad_otra'] ?? null;
        }

        unset($data['nacionalidad_otra']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Huesped {
            $correo = $data['correo_electronico'] ?? null;

            if (! $correo) {
                return Huesped::create($data);
            }

            $rolHuesped = Role::query()
                ->where('nombre', 'HUESPED')
                ->first();

            if (! $rolHuesped) {
                throw ValidationException::withMessages([
                    'correo_electronico' => 'No existe el rol HUESPED para crear la cuenta de acceso.',
                ]);
            }

            $this->passwordTemporal = Str::password(12);
            $nombreCompleto = $this->nombreCompleto($data);

            $this->usuarioHuesped = User::create([
                'role_id' => $rolHuesped->id,
                'nombres' => $data['nombres'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?? null,
                'name' => $nombreCompleto,
                'email' => $correo,
                'password' => Hash::make($this->passwordTemporal),
                'estado' => true,
            ]);

            $data['user_id'] = $this->usuarioHuesped->id;

            return Huesped::create($data);
        });
    }

    protected function afterCreate(): void
    {
        LogSistema::registrar(
            'CREAR',
            'Huéspedes',
            'Huésped creado: ' . trim($this->record->nombres . ' ' . $this->record->apellido_paterno) . ' documento ' . $this->record->numero_documento . '.'
        );

        if (! $this->usuarioHuesped) {
            return;
        }

        LogSistema::registrar(
            'CREAR',
            'Usuarios',
            'Usuario huésped creado: ' . $this->usuarioHuesped->name . ' (' . $this->usuarioHuesped->email . ').'
        );

        try {
            Mail::to($this->usuarioHuesped->email)->send(new CredencialesUsuarioMail(
                user: $this->usuarioHuesped,
                passwordTemporal: $this->passwordTemporal ?? '',
                urlAcceso: 'http://127.0.0.1:5173/login',
            ));

            LogSistema::registrar(
                'ENVIAR_CREDENCIALES',
                'Huéspedes',
                'Correo de credenciales enviado al huésped ' . $this->usuarioHuesped->email . '.'
            );

            Notification::make()
                ->success()
                ->title('Huésped creado correctamente. Se enviaron las credenciales al correo registrado.')
                ->send();
        } catch (Throwable $e) {
            LogSistema::registrar(
                'ERROR_ENVIO_CREDENCIALES',
                'Huéspedes',
                'No se pudo enviar el correo de credenciales al huésped ' . $this->usuarioHuesped->email . '. Error: ' . $e::class . '.'
            );

            Notification::make()
                ->warning()
                ->title('Huésped creado, pero no se pudo enviar el correo de credenciales.')
                ->send();
        }
    }

    private function normalizarCorreo(?string $correo): ?string
    {
        $correo = trim((string) $correo);

        return $correo === '' ? null : mb_strtolower($correo);
    }

    private function nombreCompleto(array $data): string
    {
        return trim(
            $data['nombres'] . ' ' .
            $data['apellido_paterno'] . ' ' .
            ($data['apellido_materno'] ?? '')
        );
    }
}
