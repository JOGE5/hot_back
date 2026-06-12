<x-filament-panels::page>
    <style>
        .reporte-consumos-toolbar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .dark .reporte-consumos-toolbar {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .reporte-consumos-filtros {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .reporte-consumos-filtro {
            width: 210px;
            max-width: 100%;
        }

        .reporte-consumos-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .reporte-consumos-summary {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            color: #374151;
            font-size: 14px;
        }

        .dark .reporte-consumos-summary {
            color: #d1d5db;
        }

        .reporte-consumos-total {
            font-weight: 800;
            color: #16a34a;
        }

        .reporte-consumos-table {
            width: 100%;
            overflow-x: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .dark .reporte-consumos-table {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .reporte-consumos-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .reporte-consumos-table th,
        .reporte-consumos-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 13px;
        }

        .dark .reporte-consumos-table th,
        .dark .reporte-consumos-table td {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .reporte-consumos-table th {
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .dark .reporte-consumos-table th {
            color: #9ca3af;
        }

        .reporte-consumos-table td {
            color: #111827;
            vertical-align: top;
        }

        .dark .reporte-consumos-table td {
            color: #e5e7eb;
        }

        .reporte-consumos-money {
            white-space: nowrap;
            text-align: right !important;
        }

        .reporte-consumos-empty {
            padding: 40px 20px;
            text-align: center;
            color: #6b7280;
        }

        .dark .reporte-consumos-empty {
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            .reporte-consumos-filtro {
                width: 100%;
            }
        }
    </style>

    <div class="reporte-consumos-toolbar">
        <div class="reporte-consumos-filtros">
            <div class="reporte-consumos-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar">
                    <x-filament::input type="date" wire:model.live="fecha_desde" title="Fecha desde" />
                </x-filament::input.wrapper>
            </div>

            <div class="reporte-consumos-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar">
                    <x-filament::input type="date" wire:model.live="fecha_hasta" title="Fecha hasta" />
                </x-filament::input.wrapper>
            </div>

            <div class="reporte-consumos-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-squares-2x2">
                    <x-filament::input.select wire:model.live="plato_id">
                        <option value="">Todos los platos</option>
                        @foreach($this->platosFiltro as $platoFiltro)
                            <option value="{{ $platoFiltro->id }}">{{ $platoFiltro->nombre }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="reporte-consumos-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-user">
                    <x-filament::input.select wire:model.live="huesped_id">
                        <option value="">Todos los huéspedes</option>
                        @foreach($this->huespedesFiltro as $huespedFiltro)
                            <option value="{{ $huespedFiltro->id }}">
                                {{ trim($huespedFiltro->nombres . ' ' . $huespedFiltro->apellido_paterno . ' ' . ($huespedFiltro->apellido_materno ?? '')) ?: 'Huésped sin nombre' }}
                                @if($huespedFiltro->numero_documento)
                                    - {{ $huespedFiltro->numero_documento }}
                                @endif
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="reporte-consumos-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-home-modern">
                    <x-filament::input.select wire:model.live="habitacion_id">
                        <option value="">Todas las habitaciones</option>
                        @foreach($this->habitacionesFiltro as $habitacionFiltro)
                            <option value="{{ $habitacionFiltro->id }}">
                                Hab. {{ $habitacionFiltro->numero }}@if($habitacionFiltro->tipo) - {{ $habitacionFiltro->tipo }}@endif
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="reporte-consumos-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-funnel">
                    <x-filament::input.select wire:model.live="estado">
                        <option value="">Todos los estados</option>
                        <option value="Confirmado">Confirmado</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Anulado">Anulado</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <x-filament::button color="gray" wire:click="limpiarFiltros">
                Limpiar filtros
            </x-filament::button>

            <div class="reporte-consumos-actions">
                <x-filament::button color="danger" icon="heroicon-o-document-text" wire:click="exportarPdf">
                    Exportar PDF
                </x-filament::button>

                <x-filament::button color="success" icon="heroicon-o-document-arrow-down" wire:click="exportarExcel">
                    Exportar Excel
                </x-filament::button>
            </div>
        </div>
    </div>

    <div class="reporte-consumos-summary">
        <div>Registros encontrados: <strong>{{ $this->consumos->count() }}</strong></div>
        <div class="reporte-consumos-total">Total general confirmado: Bs. {{ number_format($this->totalGeneralConfirmado, 2) }}</div>
    </div>

    <div class="reporte-consumos-table">
        @if($this->consumos->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Fecha de consumo</th>
                        <th>Huésped</th>
                        <th>Habitación</th>
                        <th>Plato</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Registrado por</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->consumos as $consumo)
                        <tr>
                            <td>{{ $consumo->fecha_consumo ? \Carbon\Carbon::parse($consumo->fecha_consumo)->format('d/m/Y H:i') : 'Sin fecha' }}</td>
                            <td>{{ $consumo->huesped_nombre }}</td>
                            <td>{{ $consumo->habitacion_texto }}</td>
                            <td>{{ $consumo->plato_nombre }}</td>
                            <td>{{ (int) $consumo->cantidad }}</td>
                            <td class="reporte-consumos-money">Bs. {{ number_format((float) $consumo->precio_unitario, 2) }}</td>
                            <td class="reporte-consumos-money">Bs. {{ number_format((float) $consumo->total, 2) }}</td>
                            <td>{{ $consumo->estado }}</td>
                            <td>{{ $consumo->registrado_por_nombre }}</td>
                            <td>{{ $consumo->observacion ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="reporte-consumos-empty">
                No hay consumos para los filtros seleccionados.
            </div>
        @endif
    </div>
</x-filament-panels::page>
