<?php

namespace App\Livewire\Admin;

use App\Support\Admin\AlertasOperativas;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Support\Enums\Alignment;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NotificacionesOperativas extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function verNotificacionesAction(): Action
    {
        return Action::make('verNotificaciones')
            ->label('Notificaciones operativas')
            ->icon('heroicon-o-bell')
            ->iconButton()
            ->color(fn (): string => $this->totalAlertas() > 0 ? 'warning' : 'gray')
            ->badge(fn (): ?string => $this->totalAlertas() > 0 ? (string) $this->totalAlertas() : null)
            ->badgeColor('danger')
            ->tooltip('Notificaciones operativas')
            ->extraAttributes([
                'class' => 'admin-operational-notifications-button',
            ])
            ->modalHeading('Notificaciones operativas')
            ->modalWidth('lg')
            ->modalAlignment(Alignment::Center)
            ->modalSubmitAction(false)
            ->modalCancelAction(fn (Action $action): Action => $action
                ->label('Cerrar')
                ->color('gray'))
            ->modalContent(fn (): View => view('filament.admin.notificaciones-operativas', [
                'alertas' => $this->alertas(),
                'total' => $this->totalAlertas(),
            ]));
    }

    public function render(): View
    {
        return view('livewire.admin.notificaciones-operativas');
    }

    /**
     * @return array<int, array{key: string, titulo: string, descripcion: string, total: int, color: string, roles: array<int, string>}>
     */
    private function alertas(): array
    {
        return AlertasOperativas::paraUsuario(auth()->user());
    }

    private function totalAlertas(): int
    {
        return AlertasOperativas::totalParaUsuario(auth()->user());
    }
}
