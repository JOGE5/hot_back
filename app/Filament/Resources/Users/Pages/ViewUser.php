<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar')
                ->visible(fn (): bool => UserResource::puedeGestionarUsuario($this->record, permitirPropio: false)),

            DeleteAction::make()
                ->label('Dar de baja')
                ->modalHeading('Dar de baja usuario administrativo')
                ->modalDescription('El usuario será enviado a la papelera y podrá recuperarse posteriormente.')
                ->modalSubmitActionLabel('Sí, dar de baja')
                ->successNotificationTitle('Usuario dado de baja correctamente')
                ->visible(fn (): bool => UserResource::puedeGestionarUsuario($this->record, permitirPropio: false)),
        ];
    }
}