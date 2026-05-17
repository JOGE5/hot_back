<x-filament-panels::page>
    <style>
        .reservaciones-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
            background-color: #ffffff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }
        
        .dark .reservaciones-toolbar {
            background-color: #111827;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .reservaciones-filtros {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            flex-grow: 1;
        }

        .reservaciones-buscador {
            width: 320px;
            max-width: 100%;
        }

        .reservaciones-select {
            width: 200px;
            max-width: 100%;
        }

        @media (max-width: 768px) {
            .reservaciones-toolbar {
                align-items: stretch;
            }

            .reservaciones-filtros {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .reservaciones-buscador,
            .reservaciones-select {
                width: 100%;
            }
        }

        .hotel-grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            width: 100%;
        }

        .e-card {
            background: #1f2937;
            box-shadow: 0px 8px 28px -9px rgba(0, 0, 0, 0.45);
            position: relative;
            min-width: 320px;
            max-width: 100%; 
            min-height: 280px;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid var(--card-accent, #6b7280);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin: 0 auto;
            width: 100%;
        }

        .e-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            border-color: var(--card-accent);
        }

        .wave {
            position: absolute;
            width: 500px;
            height: 500px;
            opacity: 0.15;
            left: 0;
            top: 0;
            margin-left: -40%;
            margin-top: -50%;
            background: radial-gradient(circle at center, var(--card-accent) 0%, transparent 70%);
            border-radius: 40%;
            animation: wave 45s infinite linear;
            z-index: 0;
        }

        .wave:nth-child(2) {
            top: 160px;
            animation-duration: 50s;
            opacity: 0.1;
        }

        .wave:nth-child(3) {
            top: 180px;
            animation-duration: 55s;
            opacity: 0.05;
        }

        @keyframes wave {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .infotop {
            position: relative;
            z-index: 10;
            padding: 18px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .card-title {
            color: #ffffff;
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .badge-estado {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .badge-tipo {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid currentColor;
            margin-bottom: 12px;
        }

        .card-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            color: #e5e7eb;
            font-size: 0.85rem;
        }

        .detail-row svg {
            width: 18px !important;
            height: 18px !important;
            min-width: 18px;
            color: var(--card-accent);
            margin-right: 8px;
        }

        .card-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 12px;
        }

        .empty-reservaciones {
            min-height: 260px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: center;
            color: #d1d5db;
        }

        .empty-reservaciones-icon {
            width: 64px !important;
            height: 64px !important;
            color: #d6a84f;
            opacity: 0.85;
        }

        .empty-reservaciones-title {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
        }

        .empty-reservaciones-text {
            font-size: 14px;
            color: #9ca3af;
            max-width: 360px;
        }
    </style>

    <div class="reservaciones-toolbar">
        <div class="reservaciones-filtros">
            <div class="reservaciones-buscador">
                <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                    <x-filament::input 
                        type="text" 
                        wire:model.live.debounce.300ms="buscar" 
                        placeholder="Buscar huésped o habitación..." 
                    />
                </x-filament::input.wrapper>
            </div>
            
            <div class="reservaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-funnel">
                    <x-filament::input.select wire:model.live="estado_reservacion">
                        <option value="">Estado Reserva</option>
                        <option value="PENDIENTE DE PAGO">Pendiente de pago</option>
                        <option value="CONFIRMADA">Confirmada</option>
                        <option value="EN ESTADÍA">En estadía</option>
                        <option value="CANCELADA">Cancelada</option>
                        <option value="FINALIZADA">Finalizada</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="reservaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-credit-card">
                    <x-filament::input.select wire:model.live="estado_pago">
                        <option value="">Estado Pago</option>
                        <option value="PENDIENTE">Pendiente</option>
                        <option value="CONFIRMADO">Confirmado</option>
                        <option value="RECHAZADO">Rechazado</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div style="display: flex; gap: 8px;">
                <button 
                    type="button" 
                    wire:click="toggleOrigen('En línea')"
                    style="padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; 
                           @if($origen_reservacion === 'En línea') background-color: #2563eb; color: #ffffff; border: 1px solid #2563eb; 
                           @else background-color: #1f2937; color: #e5e7eb; border: 1px solid rgba(255,255,255,0.1); @endif"
                >
                    En línea
                </button>
                
                <button 
                    type="button" 
                    wire:click="toggleOrigen('Recepción presencial')"
                    style="padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; 
                           @if($origen_reservacion === 'Recepción presencial') background-color: #d97706; color: #ffffff; border: 1px solid #d97706; 
                           @else background-color: #1f2937; color: #e5e7eb; border: 1px solid rgba(255,255,255,0.1); @endif"
                >
                    Recepción presencial
                </button>
            </div>
        </div>

        @if(App\Filament\Resources\Reservacions\ReservacionResource::canCreate())
            <div>
                <x-filament::button tag="a" href="{{ App\Filament\Resources\Reservacions\ReservacionResource::getUrl('create') }}">
                    Nueva reservación
                </x-filament::button>
            </div>
        @endif
    </div>

    <div class="hotel-grid-container">
        @forelse($this->reservaciones as $reservacion)
            @php
                $estadoColor = match ($reservacion->estado_reservacion) {
                    'PENDIENTE DE PAGO' => 'background-color: #d97706; color: #ffffff;',
                    'CONFIRMADA' => 'background-color: #16a34a; color: #ffffff;',
                    'EN ESTADÍA' => 'background-color: #2563eb; color: #ffffff;',
                    'CANCELADA' => 'background-color: #dc2626; color: #ffffff;',
                    'FINALIZADA' => 'background-color: #6b7280; color: #ffffff;',
                    default => 'background-color: #6b7280; color: #ffffff;',
                };

                $pagoColorText = match ($reservacion->estado_pago) {
                    'PENDIENTE' => '#d97706',
                    'CONFIRMADO' => '#16a34a',
                    'RECHAZADO' => '#dc2626',
                    default => '#6b7280',
                };

                $origenColorText = match ($reservacion->origen_reservacion) {
                    'EN LÍNEA' => '#2563eb',
                    'RECEPCIÓN' => '#8b5cf6',
                    default => '#6b7280',
                };

                $cardAccent = match ($reservacion->estado_reservacion) {
                    'PENDIENTE DE PAGO' => '#d97706',
                    'CONFIRMADA' => '#16a34a',
                    'EN ESTADÍA' => '#2563eb',
                    'CANCELADA' => '#dc2626',
                    'FINALIZADA' => '#6b7280',
                    default => '#6b7280',
                };
            @endphp
            
            <div class="e-card" style="--card-accent: {{ $cardAccent }};">
                <div class="wave"></div>
                <div class="wave"></div>
                <div class="wave"></div>

                <div class="infotop">
                    <div class="card-header-row">
                        <div>
                            <h3 class="card-title">Reserva #{{ $reservacion->codigo_checkin ?? $reservacion->id }}</h3>
                            <span class="text-xs text-gray-400 mt-1 block">Hab. {{ $reservacion->habitacion->numero }} - {{ $reservacion->habitacion->tipo }}</span>
                        </div>
                        <span class="badge-estado" style="{{ $estadoColor }}">
                            {{ strtoupper($reservacion->estado_reservacion) }}
                        </span>
                    </div>
                    
                    <div class="card-details">
                        <div class="detail-row">
                            <x-heroicon-o-user class="w-4 h-4" />
                            <span class="truncate">{{ $reservacion->huesped->nombres }} {{ $reservacion->huesped->apellido_paterno }} {{ $reservacion->huesped->apellido_materno }}</span>
                        </div>
                        <div class="detail-row">
                            <x-heroicon-o-identification class="w-4 h-4" />
                            <span>{{ $reservacion->huesped->numero_documento }}</span>
                        </div>
                        <div class="detail-row">
                            <x-heroicon-o-calendar class="w-4 h-4" />
                            <span>{{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y') }}</span>
                        </div>
                        <div class="detail-row">
                            <x-heroicon-o-users class="w-4 h-4" />
                            <span>{{ $reservacion->cantidad_personas }} {{ $reservacion->cantidad_personas == 1 ? 'persona' : 'personas' }}</span>
                        </div>
                    </div>

                    <div style="margin-top: auto; border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 12px; margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 0.8rem; color: #e5e7eb;">Total:</span>
                            <span style="font-size: 1.1rem; font-weight: 700; color: #ffffff;">${{ number_format($reservacion->total, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span style="font-size: 0.75rem; color: #9ca3af;">Pago:</span>
                            <span class="badge-tipo" style="color: {{ $pagoColorText }}; border-color: {{ $pagoColorText }}; margin-bottom: 0;">{{ $reservacion->estado_pago }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.75rem; color: #9ca3af;">Origen: <span style="color: {{ $origenColorText }}; font-weight: 600;">{{ $reservacion->origen_reservacion }}</span></span>
                            <span style="font-size: 0.75rem; color: #9ca3af;">{{ $reservacion->metodo_pago ?? 'Sin método' }}</span>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <x-filament::button 
                            tag="a" 
                            href="{{ App\Filament\Resources\Reservacions\ReservacionResource::getUrl('view', ['record' => $reservacion]) }}"
                            color="gray"
                            size="sm"
                            class="flex-1"
                        >
                            Ver
                        </x-filament::button>

                        @if(App\Filament\Resources\Reservacions\ReservacionResource::canEdit($reservacion))
                            <x-filament::button 
                                tag="a" 
                                href="{{ App\Filament\Resources\Reservacions\ReservacionResource::getUrl('edit', ['record' => $reservacion]) }}"
                                color="warning"
                                size="sm"
                                class="flex-1"
                            >
                                Editar
                            </x-filament::button>
                        @endif
                        
                        @if($reservacion->estado_pago === 'Confirmado' && $reservacion->total > 0 && !empty($reservacion->codigo_checkin))
                            <x-filament::button 
                                tag="a" 
                                href="{{ route('admin.reservaciones.recibo', $reservacion) }}"
                                color="success"
                                size="sm"
                                class="flex-1"
                                target="_blank"
                            >
                                Recibo
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-reservaciones" style="grid-column: 1 / -1; background-color: #1f2937; border-radius: 16px; border: 1px dashed rgba(255,255,255,0.1);">
                <x-heroicon-o-calendar class="empty-reservaciones-icon" />
                <h3 class="empty-reservaciones-title">No se encontraron reservaciones</h3>
                <p class="empty-reservaciones-text">Intenta cambiar los filtros o registra una nueva reservación.</p>
            </div>
        @endforelse
    </div>
    
    <div style="margin-top: 24px;">
        {{ $this->reservaciones->links() }}
    </div>
</x-filament-panels::page>
