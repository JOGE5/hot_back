@php
    $record = $getRecord();

    $estadoClasses = [
        'Borrador' => 'menu-badge-borrador',
        'Publicado' => 'menu-badge-publicado',
        'Archivado' => 'menu-badge-archivado',
    ];

    $estadoClass = $estadoClasses[$record->estado] ?? 'menu-badge-archivado';
    $platos = $record->platos->sortBy('pivot.orden');
@endphp

<style>
    .menu-card {
        width: 100%;
        min-height: 285px;
        padding: 18px;
        border-radius: 14px;
        background: linear-gradient(180deg, #fffaf0 0%, #f6efe1 100%);
        border: 1px solid rgba(120, 87, 44, 0.18);
        box-shadow: 0 14px 35px rgba(70, 50, 25, 0.12);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .menu-card-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .menu-date {
        color: #6f4f24;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .menu-title {
        color: #2f2618;
        font-size: 20px;
        font-weight: 800;
        line-height: 1.2;
        margin-top: 4px;
    }

    .menu-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .menu-badge-borrador {
        color: #7c5b1e;
        background: rgba(234, 179, 8, 0.16);
        border-color: rgba(161, 98, 7, 0.22);
    }

    .menu-badge-publicado {
        color: #166534;
        background: rgba(34, 197, 94, 0.16);
        border-color: rgba(22, 101, 52, 0.22);
    }

    .menu-badge-archivado {
        color: #475569;
        background: rgba(100, 116, 139, 0.14);
        border-color: rgba(71, 85, 105, 0.20);
    }

    .menu-meta {
        display: grid;
        gap: 7px;
        color: #5f5140;
        font-size: 13px;
    }

    .menu-meta strong {
        color: #2f2618;
    }

    .menu-platos {
        flex: 1;
        border-top: 1px solid rgba(120, 87, 44, 0.16);
        padding-top: 12px;
    }

    .menu-platos-title {
        color: #6f4f24;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .menu-platos-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .menu-plato-chip {
        max-width: 100%;
        border-radius: 999px;
        padding: 4px 9px;
        color: #3b2f1f;
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(120, 87, 44, 0.14);
        font-size: 12px;
        font-weight: 700;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .menu-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 2px;
    }

    .menu-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .menu-btn-secondary {
        color: #6f4f24;
        background: rgba(255, 255, 255, 0.62);
        border: 1px solid rgba(120, 87, 44, 0.18);
    }

    .menu-btn-primary {
        color: #ffffff;
        background: #8a5a21;
        border: 1px solid rgba(80, 48, 12, 0.18);
    }

    .menu-btn-primary:hover {
        background: #6f4617;
    }
</style>

<div class="menu-card">
    <div class="menu-card-top">
        <div>
            <div class="menu-date">
                {{ optional($record->fecha_menu)->format('d/m/Y') }}
            </div>
            <div class="menu-title">
                {{ $record->tipo_menu }}
            </div>
        </div>

        <div class="menu-badge {{ $estadoClass }}">
            {{ $record->estado }}
        </div>
    </div>

    <div class="menu-meta">
        <div>
            <strong>Chef:</strong> {{ $record->chef?->name ?: 'Sin asignar' }}
        </div>
        <div>
            <strong>Platos:</strong> {{ $platos->count() }}
        </div>
        @if($record->estado === 'Publicado')
            <div>
                <strong>Indicador:</strong> Publicado
            </div>
        @endif
    </div>

    <div class="menu-platos" id="menu-platos-{{ $record->id }}">
        <div class="menu-platos-title">
            Platos del menu
        </div>

        <div class="menu-platos-list">
            @forelse($platos->take(4) as $plato)
                <div class="menu-plato-chip" title="{{ $plato->nombre }}">
                    {{ $plato->nombre }}
                </div>
            @empty
                <div class="menu-plato-chip">
                    Sin platos
                </div>
            @endforelse

            @if($platos->count() > 4)
                <div class="menu-plato-chip">
                    +{{ $platos->count() - 4 }} mas
                </div>
            @endif
        </div>
    </div>

    <div class="menu-actions">
        <a
            href="{{ \App\Filament\Resources\Menus\MenuResource::getUrl('edit', ['record' => $record]) }}#menu-platos-{{ $record->id }}"
            class="menu-btn menu-btn-secondary"
        >
            Ver platos
        </a>

        <a
            href="{{ \App\Filament\Resources\Menus\MenuResource::getUrl('edit', ['record' => $record]) }}"
            class="menu-btn menu-btn-primary"
        >
            Editar
        </a>
    </div>
</div>
