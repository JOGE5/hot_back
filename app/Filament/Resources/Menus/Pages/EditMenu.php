<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use App\Support\LogSistema;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected array $platosMenu = [];

    protected ?string $estadoAnterior = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(function (): void {
                    LogSistema::registrar(
                        'ELIMINAR',
                        'Menús',
                        'Menú eliminado: ' . $this->record->tipo_menu . ' del ' . $this->record->fecha_menu?->format('Y-m-d') . '.'
                    );
                }),
            ForceDeleteAction::make(),
            RestoreAction::make()
                ->after(function (): void {
                    LogSistema::registrar(
                        'RESTAURAR',
                        'Menús',
                        'Menú restaurado: ' . $this->record->tipo_menu . ' del ' . $this->record->fecha_menu?->format('Y-m-d') . '.'
                    );
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['platos_menu'] = $this->getRecord()
            ->platos()
            ->orderBy('menu_plato.orden')
            ->get()
            ->map(fn ($plato): array => [
                'plato_id' => $plato->id,
                'orden' => $plato->pivot->orden,
            ])
            ->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->estadoAnterior = $this->record->estado;
        $this->platosMenu = $data['platos_menu'] ?? [];

        unset($data['platos_menu']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);

        $record->platos()->sync($this->getPlatosSyncData());

        return $record;
    }

    protected function afterSave(): void
    {
        LogSistema::registrar(
            'EDITAR',
            'Menús',
            'Menú actualizado: ' . $this->record->tipo_menu . ' del ' . $this->record->fecha_menu?->format('Y-m-d') . '.'
        );

        if ($this->estadoAnterior !== 'Publicado' && $this->record->estado === 'Publicado') {
            LogSistema::registrar(
                'PUBLICAR',
                'Menús',
                'Menú publicado: ' . $this->record->tipo_menu . ' del ' . $this->record->fecha_menu?->format('Y-m-d') . '.'
            );
        }

        if ($this->estadoAnterior !== 'Archivado' && $this->record->estado === 'Archivado') {
            LogSistema::registrar(
                'ARCHIVAR',
                'Menús',
                'Menú archivado: ' . $this->record->tipo_menu . ' del ' . $this->record->fecha_menu?->format('Y-m-d') . '.'
            );
        }
    }

    protected function getPlatosSyncData(): array
    {
        $syncData = [];

        foreach ($this->platosMenu as $item) {
            if (empty($item['plato_id'])) {
                continue;
            }

            $syncData[$item['plato_id']] = [
                'orden' => max(1, (int) ($item['orden'] ?? 1)),
            ];
        }

        return $syncData;
    }
}
