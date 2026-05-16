<?php

namespace App\Filament\Resources\HabitacionResource\Pages;

use App\Filament\Resources\HabitacionResource;
use App\Models\Habitacion;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class ListHabitacions extends Page
{
    protected static string $resource = HabitacionResource::class;

    protected string $view = 'filament.resources.habitacion-resource.pages.list-habitaciones';

    protected static ?string $title = 'Habitaciones';
    
    protected ?string $heading = 'Habitaciones';

    public ?string $buscar = null;
    public ?string $estado = null;
    public ?string $tipo = null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva habitación')
                ->visible(fn (): bool => HabitacionResource::canCreate()),
        ];
    }

    public function getHabitacionesProperty(): Collection
    {
        $query = Habitacion::query();

        if (!empty($this->buscar)) {
            $query->where('numero', 'like', '%' . $this->buscar . '%');
        }

        if (!empty($this->estado)) {
            $query->where('estado', $this->estado);
        }

        if (!empty($this->tipo)) {
            $query->where('tipo', $this->tipo);
        }

        return $query->orderBy('numero')->get();
    }
}
