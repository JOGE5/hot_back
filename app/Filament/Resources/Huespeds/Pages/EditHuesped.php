<?php

namespace App\Filament\Resources\Huespeds\Pages;

use App\Filament\Resources\Huespeds\HuespedResource;
use App\Support\LogSistema;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHuesped extends EditRecord
{
    protected static string $resource = HuespedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $opcionesNacionalidad = [
            'Bolivia', 'Argentina', 'Brasil', 'Chile', 'Colombia', 'Ecuador',
            'Paraguay', 'Perú', 'Uruguay', 'Venezuela', 'México', 'Estados Unidos', 'España',
        ];

        if (isset($data['nacionalidad']) && ! in_array($data['nacionalidad'], $opcionesNacionalidad, true)) {
            $data['nacionalidad_otra'] = $data['nacionalidad'];
            $data['nacionalidad'] = 'Otra';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->normalizarDatosPersonales($data);
        $data['correo_electronico'] = $this->normalizarCorreo($data['correo_electronico'] ?? null);

        if (($data['nacionalidad'] ?? '') === 'Otra') {
            $data['nacionalidad'] = $data['nacionalidad_otra'] ?? null;
        }

        unset($data['nacionalidad_otra']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->wasChanged('correo_electronico') && $this->record->user) {
            $this->record->user->forceFill([
                'email' => $this->record->correo_electronico,
            ])->save();
        }

        LogSistema::registrar(
            'EDITAR',
            'Huéspedes',
            'Huésped editado: ' . trim($this->record->nombres . ' ' . $this->record->apellido_paterno) . ' documento ' . $this->record->numero_documento . '.'
        );
    }

    private function normalizarCorreo(?string $correo): ?string
    {
        $correo = trim((string) $correo);

        return $correo === '' ? null : mb_strtolower($correo);
    }

    private function normalizarDatosPersonales(array $data): array
    {
        foreach (['nombres', 'apellido_paterno', 'apellido_materno', 'telefono'] as $campo) {
            if (array_key_exists($campo, $data)) {
                $data[$campo] = $this->normalizarTexto($data[$campo] ?? null);
            }
        }

        return $data;
    }

    private function normalizarTexto(?string $texto): ?string
    {
        $texto = trim((string) $texto);

        return $texto === '' ? null : $texto;
    }
}
