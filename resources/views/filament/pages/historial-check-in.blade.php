<x-filament-panels::page>
    <style>
        .historial-toolbar {
            background: #18181b;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .historial-filters-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .historial-search {
            flex: 1;
            min-width: 250px;
        }

        .historial-filter {
            width: 180px;
        }

        .historial-date {
            width: 160px;
        }

        .historial-actions {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }

        .historial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
        }

        .historial-card {
            background: #18181b;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.2s ease;
            position: relative;
        }

        .historial-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .historial-header {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: rgba(255, 255, 255, 0.02);
        }

        .historial-code {
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
        }

        .historial-date-badge {
            font-size: 11px;
            color: #a1a1aa;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .historial-status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .historial-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .historial-row {
            display: flex;
            gap: 12px;
        }

        .historial-icon {
            width: 18px;
            height: 18px;
            color: #a1a1aa;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .historial-text {
            color: #e5e7eb;
            font-size: 13px;
            line-height: 1.4;
        }

        .historial-label {
            color: #71717a;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .historial-footer {
            padding: 12px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .historial-total {
            color: #10b981;
            font-weight: 800;
            font-size: 15px;
        }

        .historial-user {
            color: #a1a1aa;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .empty-state {
            grid-column: 1 / -1;
            justify-self: center;
            width: 100%;
            max-width: 520px;
            margin: 20px 0;
            padding: 40px 20px;
            text-align: center;
            background: #18181b;
            border: 1px dashed rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 1024px) {
            .historial-filters-row {
                flex-direction: column;
                align-items: stretch;
            }
            .historial-actions {
                margin-left: 0;
                margin-top: 10px;
                justify-content: flex-end;
            }
            .historial-filter, .historial-date {
                width: 100%;
            }
        }
    </style>

    <div class="historial-toolbar">
        <div class="historial-filters-row">
            <div class="historial-search">
                <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                    <x-filament::input 
                        type="text" 
                        wire:model.live.debounce.300ms="buscar" 
                        placeholder="Buscar por código, huésped, documento, habitación..." 
                    />
                </x-filament::input.wrapper>
            </div>

            <div class="historial-filter">
                <x-filament::input.wrapper prefix-icon="heroicon-o-funnel">
                    <x-filament::input.select wire:model.live="estado_reservacion">
                        <option value="">Estado Reserva</option>
                        <option value="En estadía">En estadía</option>
                        <option value="Finalizada">Finalizada</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="historial-filter">
                <x-filament::input.wrapper prefix-icon="heroicon-o-credit-card">
                    <x-filament::input.select wire:model.live="metodo_pago">
                        <option value="">Método Pago</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="QR">QR</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="historial-date">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar">
                    <x-filament::input 
                        type="date" 
                        wire:model.live="fecha_desde" 
                        title="Check-in desde"
                    />
                </x-filament::input.wrapper>
            </div>

            <div class="historial-date">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar">
                    <x-filament::input 
                        type="date" 
                        wire:model.live="fecha_hasta" 
                        title="Check-in hasta"
                    />
                </x-filament::input.wrapper>
            </div>

            <div class="historial-date">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar-days">
                    <x-filament::input 
                        type="date" 
                        wire:model.live="fecha_checkout_desde" 
                        title="Check-out desde"
                    />
                </x-filament::input.wrapper>
            </div>

            <div class="historial-date">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar-days">
                    <x-filament::input 
                        type="date" 
                        wire:model.live="fecha_checkout_hasta" 
                        title="Check-out hasta"
                    />
                </x-filament::input.wrapper>
            </div>

            <div class="historial-filter">
                <x-filament::input.wrapper prefix-icon="heroicon-o-identification">
                    <x-filament::input.select wire:model.live="checkin_user_id">
                        <option value="">Usuario check-in</option>
                        @foreach($this->usuariosCheck as $usuarioCheck)
                            <option value="{{ $usuarioCheck->id }}">{{ $usuarioCheck->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="historial-filter">
                <x-filament::input.wrapper prefix-icon="heroicon-o-identification">
                    <x-filament::input.select wire:model.live="checkout_user_id">
                        <option value="">Usuario check-out</option>
                        @foreach($this->usuariosCheck as $usuarioCheck)
                            <option value="{{ $usuarioCheck->id }}">{{ $usuarioCheck->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="historial-actions">
                <x-filament::button wire:click="exportarExcel" color="success" icon="heroicon-o-document-arrow-down">
                    Excel
                </x-filament::button>
                <x-filament::button wire:click="exportarPdf" color="danger" icon="heroicon-o-document-text">
                    PDF
                </x-filament::button>
            </div>
        </div>
    </div>

    <div class="historial-grid">
        @forelse($this->reservaciones as $reservacion)
            @php
                $statusColor = match($reservacion->estado_reservacion) {
                    'En estadía' => 'background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);',
                    'Finalizada' => 'background: rgba(156, 163, 175, 0.15); color: #9ca3af; border: 1px solid rgba(156, 163, 175, 0.3);',
                    default => 'background: rgba(255, 255, 255, 0.1); color: #fff;',
                };
            @endphp
            <div class="historial-card">
                @php
                    $huesped = $reservacion->huesped;
                    $nombreHuesped = $huesped
                        ? ($huesped->trashed() ? 'Huésped dado de baja' : trim(sprintf('%s %s %s', $huesped->nombres, $huesped->apellido_paterno, $huesped->apellido_materno)))
                        : 'Huésped no disponible';
                    $documentoHuesped = $huesped?->numero_documento ?? 'Documento no disponible';

                    $habitacion = $reservacion->habitacion;
                    $textoHabitacion = $habitacion
                        ? ($habitacion->trashed() ? 'Habitación dada de baja' : sprintf('Hab. %s (%s)', $habitacion->numero, $habitacion->tipo))
                        : 'Habitación no disponible';
                @endphp
                <div class="historial-header">
                    <div>
                        <div class="historial-code">{{ $reservacion->codigo_checkin }}</div>
                        <div class="historial-date-badge">
                            <x-heroicon-o-arrow-right-end-on-rectangle class="w-3 h-3 text-green-500" />
                            IN: {{ \Carbon\Carbon::parse($reservacion->checkin_at)->format('d/m/Y H:i') }}
                        </div>
                        @if($reservacion->codigo_checkout)
                            <div class="historial-date-badge mt-1">
                                <x-heroicon-o-arrow-left-start-on-rectangle class="w-3 h-3 text-red-400" />
                                OUT: {{ \Carbon\Carbon::parse($reservacion->checkout_at)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </div>
                    <div style="text-align: right;">
                        <div class="historial-status mb-2" style="{!! $statusColor !!}">
                            {{ $reservacion->estado_reservacion }}
                        </div>
                        @if($reservacion->codigo_checkout)
                            <div class="text-[10px] font-bold text-gray-500 mt-1 uppercase">{{ $reservacion->codigo_checkout }}</div>
                        @endif
                    </div>
                </div>

                <div class="historial-body">
                    <div class="historial-row">
                        <x-heroicon-o-user class="historial-icon" />
                        <div>
                            <div class="historial-label">Huésped</div>
                            <div class="historial-text font-bold">
                                {{ $nombreHuesped }}
                            </div>
                            <div class="historial-text text-xs text-gray-400 mt-0.5">
                                {{ $documentoHuesped }}
                            </div>
                        </div>
                    </div>

                    <div class="historial-row">
                        <x-heroicon-o-home-modern class="historial-icon" />
                        <div>
                            <div class="historial-label">Habitación</div>
                            <div class="historial-text">
                                {{ $textoHabitacion }}
                            </div>
                            <div class="historial-text text-xs text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $checkinUserName = $reservacion->checkinUser?->name ? explode(' ', $reservacion->checkinUser->name)[0] : null;
                @endphp
                <div class="historial-footer">
                    <div class="historial-total">
                        Bs. {{ number_format($reservacion->total, 2) }} <span class="text-xs font-normal text-gray-400">({{ $reservacion->metodo_pago }})</span>
                    </div>
                    @if($checkinUserName)
                        <div class="historial-user">
                            <x-heroicon-o-identification class="w-3 h-3" />
                            {{ $checkinUserName }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg style="width: 48px; height: 48px; margin: 0 auto 16px auto; color: #a1a1aa;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin-bottom: 6px;">No se encontraron check-ins</h3>
                <p style="font-size: 13px; color: #a1a1aa; line-height: 1.4;">Ajusta los filtros o realiza un check-in para ver resultados.</p>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
