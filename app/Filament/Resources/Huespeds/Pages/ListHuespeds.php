<?php

namespace App\Filament\Resources\Huespeds\Pages;

use App\Filament\Resources\Huespeds\HuespedResource;
use App\Filament\Resources\HuespedesEliminados\HuespedEliminadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Actions\Action;
use Filament\Facades\Filament;

class ListHuespeds extends ListRecords
{
    protected static string $resource = HuespedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportar_excel')
                ->label('Exportar Excel')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->url(route('admin.reportes.huespedes.excel'))
                ->openUrlInNewTab()
                ->visible(function () {
                    $user = Filament::auth()->user();
                    return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN']);
                }),

            Action::make('exportar_pdf')
                ->label('Exportar PDF')
                ->color('danger')
                ->icon('heroicon-o-document-text')
                ->url(route('admin.reportes.huespedes.pdf'))
                ->openUrlInNewTab()
                ->visible(function () {
                    $user = Filament::auth()->user();
                    return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN']);
                }),

            Action::make('papelera_huespedes')
                ->label('Papelera de huéspedes')
                ->color('gray')
                ->icon('heroicon-o-trash')
                ->url(HuespedEliminadoResource::getUrl('index'))
                ->visible(function () {
                    $user = Filament::auth()->user();
                    return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN']);
                }),

            CreateAction::make()
                ->label('Nuevo huésped')
                ->visible(fn (): bool => HuespedResource::canCreate()),
        ];
    }
}