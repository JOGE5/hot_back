<x-filament-panels::page>
    <style>
        .consumos-populares-toolbar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .dark .consumos-populares-toolbar {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .consumos-populares-filtros {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .consumos-populares-filtro {
            width: 210px;
            max-width: 100%;
        }

        .consumos-populares-table {
            width: 100%;
            overflow-x: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .dark .consumos-populares-table {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .consumos-populares-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        .consumos-populares-table th,
        .consumos-populares-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
        }

        .dark .consumos-populares-table th,
        .dark .consumos-populares-table td {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .consumos-populares-table th {
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .dark .consumos-populares-table th {
            color: #9ca3af;
        }

        .consumos-populares-table td {
            color: #111827;
        }

        .dark .consumos-populares-table td {
            color: #e5e7eb;
        }

        .consumos-populares-puesto {
            width: 64px;
            font-weight: 800;
        }

        .consumos-populares-total {
            font-weight: 800;
            color: #16a34a !important;
            white-space: nowrap;
        }

        .consumos-populares-empty {
            padding: 40px 20px;
            text-align: center;
            color: #6b7280;
        }

        .dark .consumos-populares-empty {
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            .consumos-populares-filtro {
                width: 100%;
            }
        }
    </style>

    <div class="consumos-populares-toolbar">
        <div class="consumos-populares-filtros">
            <div class="consumos-populares-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar">
                    <x-filament::input type="date" wire:model.live="fecha_desde" title="Fecha desde" />
                </x-filament::input.wrapper>
            </div>

            <div class="consumos-populares-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar">
                    <x-filament::input type="date" wire:model.live="fecha_hasta" title="Fecha hasta" />
                </x-filament::input.wrapper>
            </div>

            <div class="consumos-populares-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-squares-2x2">
                    <x-filament::input.select wire:model.live="plato_id">
                        <option value="">Todos los platos</option>
                        @foreach($this->platosFiltro as $platoFiltro)
                            <option value="{{ $platoFiltro->id }}">{{ $platoFiltro->nombre }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="consumos-populares-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-user">
                    <x-filament::input.select wire:model.live="huesped_id">
                        <option value="">Todos los huespedes</option>
                        @foreach($this->huespedesFiltro as $huespedFiltro)
                            <option value="{{ $huespedFiltro->id }}">
                                {{ trim($huespedFiltro->nombres . ' ' . $huespedFiltro->apellido_paterno . ' ' . ($huespedFiltro->apellido_materno ?? '')) ?: 'Huesped sin nombre' }}
                                @if($huespedFiltro->numero_documento)
                                    - {{ $huespedFiltro->numero_documento }}
                                @endif
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="consumos-populares-filtro">
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

            <div class="consumos-populares-filtro">
                <x-filament::input.wrapper prefix-icon="heroicon-o-funnel">
                    <x-filament::input.select wire:model.live="estado">
                        <option value="">Confirmado por defecto</option>
                        <option value="Confirmado">Confirmado</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Anulado">Anulado</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <x-filament::button color="gray" wire:click="limpiarFiltros">
                Limpiar filtros
            </x-filament::button>
        </div>
    </div>

    <div class="consumos-populares-table">
        @if($this->platosPopulares->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Puesto</th>
                        <th>Plato</th>
                        <th>Cantidad total vendida</th>
                        <th>Total generado</th>
                        <th>Numero de consumos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->platosPopulares as $platoPopular)
                        <tr>
                            <td class="consumos-populares-puesto">{{ $loop->iteration }}</td>
                            <td>{{ $platoPopular->plato_nombre }}</td>
                            <td>{{ (int) $platoPopular->cantidad_total_vendida }} unidades</td>
                            <td class="consumos-populares-total">Bs. {{ number_format((float) $platoPopular->total_generado, 2) }}</td>
                            <td>{{ (int) $platoPopular->numero_consumos }} consumos</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="consumos-populares-empty">
                No hay consumos para los filtros seleccionados.
            </div>
        @endif
    </div>
</x-filament-panels::page>
