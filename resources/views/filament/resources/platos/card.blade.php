@php
    $record = $getRecord();

    $badgeColors = [
        'Disponible' => 'plato-badge-disponible',
        'No disponible' => 'plato-badge-no-disponible',
        'En preparación' => 'plato-badge-preparacion',
        'Archivado' => 'plato-badge-archivado',
    ];

    $estadoClass = $badgeColors[$record->estado] ?? 'plato-badge-archivado';

    $stockStatus = $record->estado_stock_visual;
    $stockColors = [
        'Stock suficiente' => 'plato-badge-disponible',
        'Stock insuficiente' => 'plato-badge-no-disponible',
        'Sin ingredientes' => 'plato-badge-preparacion',
        'Ingredientes vencidos' => 'plato-badge-archivado',
    ];
    $stockClass = $stockColors[$stockStatus] ?? 'plato-badge-archivado';

    $imagenUrl = ! empty($record->imagen)
        ? asset('storage/' . $record->imagen)
        : null;
@endphp

<style>
    .plato-card {
        padding: 20px;
        width: 100%;
        min-height: 390px;
        border-radius: 20px;
        background: #212121;
        box-shadow: 5px 5px 8px #1b1b1b, -5px -5px 8px #272727;
        transition: 0.25s ease;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .plato-card:hover {
        transform: translateY(-6px);
    }

    .plato-card-image {
        height: 170px;
        border-radius: 15px;
        background-color: #313131;
        box-shadow: inset 5px 5px 3px #2f2f2f, inset -5px -5px 3px #333333;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #8f8f8f;
        font-size: 13px;
        font-weight: 600;
        overflow: hidden;
    }

    .plato-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .plato-card-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
        margin-top: 16px;
    }

    .plato-card-title {
        font-size: 18px;
        font-weight: 700;
        color: #b2eccf;
        line-height: 1.25;
    }

    .plato-card-price {
        background: #2f2f2f;
        border: 1px solid rgba(255,255,255,0.08);
        color: #ffffff;
        padding: 5px 9px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }

    .plato-card-body {
        margin-top: 12px;
        color: #c7c7c7;
        font-size: 14px;
        flex-grow: 1;
    }

    .plato-card-body strong {
        color: #ffffff;
    }

    .plato-description {
        margin-top: 8px;
        color: #a9a9a9;
        font-size: 13px;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .badges-container {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }

    .plato-badge {
        display: inline-flex;
        width: fit-content;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }

    .plato-badge-disponible {
        background: rgba(34,197,94,0.18);
        color: #86efac;
        border: 1px solid rgba(34,197,94,0.35);
    }

    .plato-badge-no-disponible {
        background: rgba(239,68,68,0.18);
        color: #fca5a5;
        border: 1px solid rgba(239,68,68,0.35);
    }

    .plato-badge-preparacion {
        background: rgba(59,130,246,0.18);
        color: #93c5fd;
        border: 1px solid rgba(59,130,246,0.35);
    }

    .plato-badge-archivado {
        background: rgba(107,114,128,0.18);
        color: #d1d5db;
        border: 1px solid rgba(107,114,128,0.35);
    }

    .plato-card-footer {
        margin-top: 18px;
        display: flex;
        justify-content: flex-end;
    }

    .plato-btn-edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 34px;
        padding: 0 14px;
        border-radius: 10px;
        background: #d97706;
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .plato-btn-edit:hover {
        background: #f59e0b;
    }
</style>

<div class="plato-card">
    @if($imagenUrl)
        <div class="plato-card-image">
            <img
                src="{{ $imagenUrl }}"
                alt="{{ $record->nombre }}"
                class="plato-card-img"
            >
        </div>
    @else
        <div class="plato-card-image">
            Sin imagen
        </div>
    @endif

    <div class="plato-card-header">
        <div class="plato-card-title">
            {{ $record->nombre }}
        </div>

        <div class="plato-card-price">
            Bs. {{ number_format((float) $record->precio, 2) }}
        </div>
    </div>

    <div class="plato-card-body">
        <div class="badges-container">
            <div class="plato-badge {{ $estadoClass }}">
                {{ $record->estado }}
            </div>
            <div class="plato-badge {{ $stockClass }}" title="Estado del Inventario">
                {{ $stockStatus }}
            </div>
        </div>

        <div>
            <strong>Categoría:</strong> {{ $record->categoria }}
        </div>

        @if($record->tiempo_preparacion)
            <div>
                <strong>Tiempo:</strong> {{ $record->tiempo_preparacion }} min
            </div>
        @endif

        @if($record->chef)
            <div>
                <strong>Chef:</strong> {{ $record->chef->name }}
            </div>
        @endif

        <div class="plato-description">
            {{ $record->descripcion ?: 'Sin descripción' }}
        </div>
    </div>

    <div class="plato-card-footer">
        <a
            href="{{ \App\Filament\Resources\Platos\PlatoResource::getUrl('edit', ['record' => $record]) }}"
            class="plato-btn-edit"
        >
            Editar
        </a>
    </div>
</div>