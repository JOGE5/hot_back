<div class="fi-topbar-notificaciones-operativas flex items-center" wire:poll.60s>
    {{ $this->verNotificacionesAction }}

    <x-filament-actions::modals />
</div>
