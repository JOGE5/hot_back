@php
    $record = $getRecord();

    $imagenUrl = ! empty($record->imagen)
        ? asset('storage/' . $record->imagen)
        : null;

    $badgeColors = [
        'Disponible' => 'tour-badge-disponible',
        'No disponible' => 'tour-badge-no-disponible',
        'Archivado' => 'tour-badge-archivado',
    ];

    $estadoClass = $badgeColors[$record->estado] ?? 'tour-badge-archivado';
    $puedeEditar = \App\Filament\Resources\Tours\TourResource::canEdit($record);
@endphp

<style>
    .tour-card {
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
        min-height: 420px;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.10);
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .tour-card:hover {
        transform: translateY(-3px);
        border-color: rgba(22, 163, 74, 0.28);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.12);
    }

    .tour-card-image {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 190px;
        overflow: hidden;
        background: linear-gradient(135deg, #f8fafc, #e5e7eb);
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
    }

    .tour-card-img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tour-card-content {
        display: flex;
        flex: 1;
        flex-direction: column;
        gap: 14px;
        padding: 16px;
    }

    .tour-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .tour-card-title {
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .tour-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .tour-badge-disponible {
        background: #dcfce7;
        color: #166534;
    }

    .tour-badge-no-disponible {
        background: #fef3c7;
        color: #92400e;
    }

    .tour-badge-archivado {
        background: #e5e7eb;
        color: #374151;
    }

    .tour-card-details {
        display: grid;
        gap: 9px;
        color: #475569;
        font-size: 14px;
        line-height: 1.35;
    }

    .tour-card-detail strong {
        color: #111827;
        font-weight: 800;
    }

    .tour-card-price {
        color: #15803d;
        font-size: 20px;
        font-weight: 900;
    }

    .tour-card-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: auto;
        padding-top: 4px;
    }

    .tour-btn-edit {
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

    .tour-btn-edit:hover {
        background: #166534;
        color: #ffffff;
        transform: translateY(-1px);
    }
</style>

<div class="tour-card">
    <div class="tour-card-image">
        @if($imagenUrl)
            <img
                src="{{ $imagenUrl }}"
                alt="{{ $record->nombre }}"
                class="tour-card-img"
            >
        @else
            Sin imagen
        @endif
    </div>

    <div class="tour-card-content">
        <div class="tour-card-top">
            <div class="tour-card-title">
                {{ $record->nombre }}
            </div>

            <div class="tour-badge {{ $estadoClass }}">
                {{ $record->estado }}
            </div>
        </div>

        <div class="tour-card-details">
            <div class="tour-card-detail">
                <strong>Ubicación:</strong> {{ $record->ubicacion ?: 'No definida' }}
            </div>

            <div class="tour-card-detail">
                <strong>Duración:</strong> {{ $record->duracion ?: 'No definida' }}
            </div>

            <div class="tour-card-detail">
                <strong>Cupos disponibles:</strong> {{ $record->cupos_disponibles ?? 'Sin límite' }}
            </div>
        </div>

        <div class="tour-card-price">
            Bs. {{ number_format((float) $record->precio, 2) }}
        </div>

        @if($puedeEditar)
            <div class="tour-card-footer">
                <a
                    href="{{ \App\Filament\Resources\Tours\TourResource::getUrl('edit', ['record' => $record]) }}"
                    class="tour-btn-edit"
                >
                    Editar
                </a>
            </div>
        @endif
    </div>
</div>
