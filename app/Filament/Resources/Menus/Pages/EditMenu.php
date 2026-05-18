<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected array $platosMenu = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
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

    protected function getPlatosSyncData(): array
    {
        $syncData = [];

        foreach ($this->platosMenu as $item) {
            if (empty($item['plato_id'])) {
                continue;
            }

            $syncData[$item['plato_id']] = [
                'orden' => $item['orden'] ?? 0,
            ];
        }

        return $syncData;
    }
}
