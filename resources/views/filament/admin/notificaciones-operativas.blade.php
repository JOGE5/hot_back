@php
    $alertasPendientes = collect($alertas)->filter(fn (array $alerta): bool => $alerta['total'] > 0);
@endphp

<div class="admin-operational-notifications-modal">
    <div class="admin-operational-notifications-summary">
        @if ($alertasPendientes->isEmpty())
            No hay alertas pendientes en este momento.
        @else
            Tienes {{ $total }} {{ $total === 1 ? 'alerta pendiente' : 'alertas pendientes' }}.
        @endif
    </div>

    @if ($alertasPendientes->isEmpty())
        <div class="admin-operational-notifications-empty">
            <div class="admin-operational-notifications-empty-icon">
                <x-filament::icon
                    icon="heroicon-o-bell"
                    class="admin-operational-notifications-empty-svg"
                />
            </div>

            <div>
                <div class="admin-operational-notifications-empty-title">
                    No hay notificaciones pendientes.
                </div>

                <div class="admin-operational-notifications-empty-text">
                    Las alertas operativas aparecerán aquí cuando requieran atención.
                </div>
            </div>
        </div>
    @else
        <div class="admin-operational-notifications-list">
            @foreach ($alertasPendientes as $alerta)
                <div @class([
                    'admin-operational-notification-card',
                    'is-warning' => $alerta['color'] === 'warning',
                    'is-danger' => $alerta['color'] === 'danger',
                    'is-info' => $alerta['color'] === 'info',
                ])>
                    <div class="admin-operational-notification-accent"></div>

                    <div class="admin-operational-notification-content">
                        <div class="admin-operational-notification-title">
                            {{ $alerta['titulo'] }}
                        </div>

                        <div class="admin-operational-notification-description">
                            {{ $alerta['descripcion'] }}
                        </div>
                    </div>

                    <div class="admin-operational-notification-count">
                        <x-filament::badge :color="$alerta['color']">
                            {{ $alerta['total'] }}
                        </x-filament::badge>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
