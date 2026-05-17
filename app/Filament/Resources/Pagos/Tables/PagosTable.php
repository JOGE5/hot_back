<?php

namespace App\Filament\Resources\Pagos\Tables;

use App\Exports\PagosExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class PagosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservacion.codigo_checkin')
                    ->label('Código Check-in')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reservacion.huesped.nombres')
                    ->label('Huésped')
                    ->formatStateUsing(function ($record): string {
                        $huesped = $record->reservacion?->huesped;

                        if (! $huesped) {
                            return 'Sin huésped';
                        }

                        return trim(
                            $huesped->nombres . ' ' .
                            $huesped->apellido_paterno . ' ' .
                            ($huesped->apellido_materno ?? '')
                        );
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reservacion.huesped.numero_documento')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reservacion.habitacion.numero')
                    ->label('Habitación')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('metodo_pago')
                    ->label('Método de pago')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('BOB')
                    ->sortable(),

                TextColumn::make('estado_pago')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Confirmado' => 'success',
                        'Pendiente' => 'warning',
                        'Rechazado' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('fecha_pago')
                    ->label('Fecha de pago')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('registradoPor.nombres')
                    ->label('Registrado por')
                    ->formatStateUsing(function ($record): string {
                        $usuario = $record->registradoPor;

                        if (! $usuario) {
                            return 'No registrado';
                        }

                        return trim(
                            ($usuario->nombres ?? $usuario->name ?? '') . ' ' .
                            ($usuario->apellido_paterno ?? '')
                        );
                    })
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado_pago')
                    ->label('Estado del pago')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Confirmado' => 'Confirmado',
                        'Rechazado' => 'Rechazado',
                    ]),

                SelectFilter::make('metodo_pago')
                    ->label('Método de pago')
                    ->options([
                        'Efectivo' => 'Efectivo',
                        'QR' => 'QR',
                        'Transferencia' => 'Transferencia',
                        'Tarjeta' => 'Tarjeta',
                    ]),

                Filter::make('fecha_pago')
                    ->label('Fecha de pago')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Fecha desde'),

                        DatePicker::make('hasta')
                            ->label('Fecha hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['desde'] ?? null),
                                fn (Builder $query) => $query->whereDate('fecha_pago', '>=', $data['desde']),
                            )
                            ->when(
                                filled($data['hasta'] ?? null),
                                fn (Builder $query) => $query->whereDate('fecha_pago', '<=', $data['hasta']),
                            );
                    }),
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label('Exportar Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (ListRecords $livewire) {
                        $pagos = $livewire->getFilteredTableQuery()
                            ->with([
                                'reservacion.huesped',
                                'reservacion.habitacion',
                                'registradoPor',
                            ])
                            ->get();

                        return Excel::download(
                            new PagosExport($pagos),
                            'reporte_pagos.xlsx'
                        );
                    }),

                Action::make('export_pdf')
                    ->label('Exportar PDF')
                    ->color('danger')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (ListRecords $livewire) {
                        $pagos = $livewire->getFilteredTableQuery()
                            ->with([
                                'reservacion.huesped',
                                'reservacion.habitacion',
                                'registradoPor',
                            ])
                            ->get();

                        $pdf = Pdf::loadView('reportes.pagos_pdf', [
                            'pagos' => $pagos,
                        ])->setPaper('a4', 'landscape');

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'reporte_pagos.pdf'
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}