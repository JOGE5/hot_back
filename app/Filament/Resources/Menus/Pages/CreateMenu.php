<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected array $platosMenu = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->platosMenu = $data['platos_menu'] ?? [];

        unset($data['platos_menu']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);

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
