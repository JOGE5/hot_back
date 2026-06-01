<x-filament-panels::page>
    <style>
        .habitaciones-toolbar {
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
        
        .dark .habitaciones-toolbar {
            background-color: #111827;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .habitaciones-filtros {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            flex-grow: 1;
        }

        .habitaciones-buscador {
            width: 320px;
            max-width: 100%;
        }

        .habitaciones-select {
            width: 200px;
            max-width: 100%;
        }

        @media (max-width: 768px) {
            .habitaciones-toolbar {
                align-items: stretch;
            }

            .habitaciones-filtros {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .habitaciones-buscador,
            .habitaciones-select {
                width: 100%;
            }
        }

        .hotel-grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            width: 100%;
        }

        .e-card {
            background: #1f2937;
            box-shadow: 0px 8px 28px -9px rgba(0, 0, 0, 0.45);
            position: relative;
            min-width: 260px;
            max-width: 300px; 
            min-height: 240px;
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

        .e-card.is-inactiva {
            opacity: 0.75;
            filter: grayscale(0.5);
        }

        .wave {
            position: absolute;
            width: 420px;
            height: 420px;
            opacity: 0.15;
            left: 0;
            top: 0;
            margin-left: -45%;
            margin-top: -60%;
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
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
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
            color: var(--card-accent);
            border: 1px solid var(--card-accent);
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

        .card-description {
            font-size: 0.75rem;
            color: #9ca3af;
            line-height: 1.4;
            margin-bottom: 12px;
            flex-grow: 1;
        }

        .card-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 12px;
        }

        .empty-habitaciones {
            min-height: 260px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: center;
            color: #d1d5db;
        }

        .empty-habitaciones-icon {
            width: 64px !important;
            height: 64px !important;
            color: #d6a84f;
            opacity: 0.85;
        }

        .empty-habitaciones-title {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
        }

        .empty-habitaciones-text {
            font-size: 14px;
            color: #9ca3af;
            max-width: 360px;
        }
    </style>

    <div class="habitaciones-toolbar">
        <div class="habitaciones-filtros">
            <div class="habitaciones-buscador">
                <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                    <x-filament::input 
                        type="text" 
                        wire:model.live.debounce.300ms="buscar" 
                        placeholder="Buscar por número..." 
                    />
                </x-filament::input.wrapper>
            </div>
            
            <div class="habitaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-funnel">
                    <x-filament::input.select wire:model.live="estado">
                        <option value="">Todos los estados</option>
                        <option value="Disponible">Disponible</option>
                        <option value="Ocupada">Ocupada</option>
                        <option value="Reservada">Reservada</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                        <option value="Inactiva">Inactiva</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="habitaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-home-modern">
                    <x-filament::input.select wire:model.live="tipo">
                        <option value="">Todos los tipos</option>
                        <option value="Simple">Simple</option>
                        <option value="Doble">Doble</option>
                        <option value="Matrimonial">Matrimonial</option>
                        <option value="Familiar">Familiar</option>
                        <option value="Suite">Suite</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="habitaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-users">
                    <x-filament::input type="number" min="1" wire:model.live="capacidad" placeholder="Capacidad" />
                </x-filament::input.wrapper>
            </div>

            <div class="habitaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-banknotes">
                    <x-filament::input type="number" min="0" step="0.01" wire:model.live="precio_desde" placeholder="Precio desde" />
                </x-filament::input.wrapper>
            </div>

            <div class="habitaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-banknotes">
                    <x-filament::input type="number" min="0" step="0.01" wire:model.live="precio_hasta" placeholder="Precio hasta" />
                </x-filament::input.wrapper>
            </div>

            <div class="habitaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar">
                    <x-filament::input type="date" wire:model.live="created_desde" title="Registro desde" />
                </x-filament::input.wrapper>
            </div>

            <div class="habitaciones-select">
                <x-filament::input.wrapper prefix-icon="heroicon-o-calendar">
                    <x-filament::input type="date" wire:model.live="created_hasta" title="Registro hasta" />
                </x-filament::input.wrapper>
            </div>
        </div>
    </div>

    <div class="hotel-grid-container">
        @forelse($this->habitaciones as $habitacion)
            @php
                $estadoVisual = $habitacion->estado_visual;
                $estadoColor = match ($estadoVisual) {
                    'Disponible' => 'background-color: #16a34a; color: #ffffff;',
                    'Reservada' => 'background-color: #d97706; color: #ffffff;',
                    'Ocupada' => 'background-color: #dc2626; color: #ffffff;',
                    'Mantenimiento' => 'background-color: #2563eb; color: #ffffff;',
                    'Inactiva' => 'background-color: #6b7280; color: #ffffff;',
                    default => 'background-color: #6b7280; color: #ffffff;',
                };

                $tipoAcento = match ($habitacion->tipo) {
                    'Simple' => '#6b7280',
                    'Doble' => '#2563eb',
                    'Matrimonial' => '#b76e79',
                    'Familiar' => '#16a34a',
                    'Suite' => '#d97706',
                    default => '#6b7280',
                };

                $isActivaClass = !$habitacion->activo ? 'is-inactiva' : '';
            @endphp
            
            <div class="e-card {{ $isActivaClass }}" style="--card-accent: {{ $tipoAcento }};">
                <div class="wave"></div>
                <div class="wave"></div>
                <div class="wave"></div>

                <div class="infotop">
                    <div class="card-header-row">
                        <h3 class="card-title">Habitación {{ $habitacion->numero }}</h3>
                        <span class="badge-estado" style="{{ $estadoColor }}">
                            {{ strtoupper($estadoVisual) }}
                        </span>
                    </div>
                    
                    <div>
                        <span class="badge-tipo">Tipo: {{ $habitacion->tipo }}</span>
                    </div>

                    <div class="card-details">
                        <div class="detail-row">
                            <x-heroicon-o-users class="w-4 h-4" />
                            Capacidad: {{ $habitacion->capacidad }} {{ $habitacion->capacidad == 1 ? 'persona' : 'personas' }}
                        </div>
                        <div class="detail-row">
                            <x-heroicon-o-banknotes class="w-4 h-4" />
                            Precio: Bs. {{ number_format($habitacion->precio_noche, 2) }} / noche
                        </div>
                    </div>

                    @if($habitacion->descripcion)
                        <div class="card-description line-clamp-2">
                            {{ $habitacion->descripcion }}
                        </div>
                    @else
                        <div class="card-description"></div>
                    @endif
                    
                    <div class="card-actions">
                        <x-filament::button 
                            tag="a" 
                            href="{{ App\Filament\Resources\HabitacionResource::getUrl('view', ['record' => $habitacion]) }}"
                            color="gray"
                            size="sm"
                            class="flex-1"
                        >
                            Ver
                        </x-filament::button>

                        @if(App\Filament\Resources\HabitacionResource::canEdit($habitacion))
                            <x-filament::button 
                                tag="a" 
                                href="{{ App\Filament\Resources\HabitacionResource::getUrl('edit', ['record' => $habitacion]) }}"
                                color="warning"
                                size="sm"
                                class="flex-1"
                            >
                                Editar
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-habitaciones" style="grid-column: 1 / -1; background-color: #1f2937; border-radius: 16px; border: 1px dashed rgba(255,255,255,0.1);">
                <x-heroicon-o-home-modern class="empty-habitaciones-icon" />
                <h3 class="empty-habitaciones-title">No se encontraron habitaciones</h3>
                <p class="empty-habitaciones-text">Intenta cambiar los filtros de búsqueda o registra una nueva habitación.</p>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
