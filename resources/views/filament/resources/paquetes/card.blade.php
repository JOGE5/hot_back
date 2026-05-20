@php
    $record = $getRecord();

    $imagenUrl = ! empty($record->imagen)
        ? asset('storage/' . $record->imagen)
        : null;

    $tourNombre = $record->tour?->nombre ?? $record->tour_incluido;

    $badgeColors = [
        'Publicado' => 'paquete-badge-publicado',
        'Borrador' => 'paquete-badge-borrador',
        'Archivado' => 'paquete-badge-archivado',
    ];

    $estadoClass = $badgeColors[$record->estado] ?? 'paquete-badge-archivado';
    $puedeEditar = \App\Filament\Resources\Paquetes\PaqueteResource::canEdit($record);
@endphp

<style>
    .paquete-card {
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
        min-height: 470px;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.10);
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .paquete-card:hover {
        transform: translateY(-3px);
        border-color: rgba(22, 163, 74, 0.28);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.12);
    }

    .paquete-card-image {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 200px;
        overflow: hidden;
        background: linear-gradient(135deg, #f8fafc, #e5e7eb);
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
    }

    .paquete-card-img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .paquete-card-content {
        display: flex;
        flex: 1;
        flex-direction: column;
        gap: 14px;
        padding: 16px;
    }

    .paquete-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .paquete-card-title {
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .paquete-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .paquete-badge-publicado {
        background: #dcfce7;
        color: #166534;
    }

    .paquete-badge-borrador {
        background: #fef3c7;
        color: #92400e;
    }

    .paquete-badge-archivado {
        background: #e5e7eb;
        color: #374151;
    }

    .paquete-card-details {
        display: grid;
        gap: 9px;
        color: #475569;
        font-size: 14px;
        line-height: 1.35;
    }

    .paquete-card-detail strong {
        color: #111827;
        font-weight: 800;
    }

    .paquete-meals {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .paquete-meal {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        font-size: 12px;
        font-weight: 800;
    }

    .paquete-card-price {
        color: #15803d;
        font-size: 20px;
        font-weight: 900;
    }

    .paquete-card-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: auto;
        padding-top: 4px;
    }

    .paquete-btn-edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 16px;
        border-radius: 10px;
        background: #15803d;
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        transition: background 0.16s ease, transform 0.16s ease;
    }

    .paquete-btn-edit:hover {
        background: #166534;
        color: #ffffff;
        transform: translateY(-1px);
    }
</style>

<div class="paquete-card">
    <div class="paquete-card-image">
        @if($imagenUrl)
            <img
                src="{{ $imagenUrl }}"
                alt="{{ $record->nombre }}"
                class="paquete-card-img"
            >
        @else
            Sin imagen
        @endif
    </div>

    <div class="paquete-card-content">
        <div class="paquete-card-top">
            <div class="paquete-card-title">
                {{ $record->nombre }}
            </div>

            <div class="paquete-badge {{ $estadoClass }}">
                {{ $record->estado }}
            </div>
        </div>

        <div class="paquete-card-details">
            <div class="paquete-card-detail">
                <strong>Habitación:</strong> {{ $record->tipo_habitacion ?: 'No definida' }}
            </div>

            <div class="paquete-card-detail">
                <strong>Tour incluido:</strong> {{ $tourNombre ?: 'No incluye' }}
            </div>

            <div class="paquete-card-detail">
                <strong>Duración:</strong> {{ $record->duracion_dias }} {{ $record->duracion_dias === 1 ? 'día' : 'días' }}
            </div>
        </div>

        <div class="paquete-meals">
            @if($record->incluye_desayuno)
                <span class="paquete-meal">Desayuno</span>
            @endif

            @if($record->incluye_almuerzo)
                <span class="paquete-meal">Almuerzo</span>
            @endif

            @if($record->incluye_cena)
                <span class="paquete-meal">Cena</span>
            @endif

            @if(! $record->incluye_desayuno && ! $record->incluye_almuerzo && ! $record->incluye_cena)
                <span class="paquete-meal">Sin comidas incluidas</span>
            @endif
        </div>

        <div class="paquete-card-price">
            Bs. {{ number_format((float) $record->precio_total, 2) }}
        </div>

        @if($puedeEditar)
            <div class="paquete-card-footer">
                <a
                    href="{{ \App\Filament\Resources\Paquetes\PaqueteResource::getUrl('edit', ['record' => $record]) }}"
                    class="paquete-btn-edit"
                >
                    Editar
                </a>
            </div>
        @endif
    </div>
</div>
