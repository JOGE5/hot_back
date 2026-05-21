<div class="fi-topbar-notificaciones-operativas flex items-center" wire:poll.visible.120s>
    {{ $this->verNotificacionesAction }}

    <x-filament-actions::modals />
</div>
