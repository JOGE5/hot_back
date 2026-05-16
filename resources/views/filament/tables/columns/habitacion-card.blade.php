<style>
    .hotel-card-container {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        background-color: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        height: 100%;
    }
    .dark .hotel-card-container {
        background-color: #1f2937;
        border-color: #374151;
    }
    .hotel-card-container:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .wave-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 120px;
        background: linear-gradient(744deg, #d6a84f, #8a5a2b 60%, #f3e2b8);
        z-index: 0;
        overflow: hidden;
    }

    .wave-bg::before, .wave-bg::after {
        content: "";
        position: absolute;
        width: 200%;
        height: 200%;
        top: -50%;
        left: -50%;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 40%;
        animation: wave 12s linear infinite;
    }

    .wave-bg::after {
        background-color: rgba(255, 255, 255, 0.15);
        animation: wave 15s linear infinite;
        border-radius: 45%;
    }

    @keyframes wave {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .hotel-card-content {
        position: relative;
        z-index: 10;
        padding: 1.25rem;
        padding-top: 2rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .hotel-card-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }
    .dark .hotel-card-header {
        background: rgba(31, 41, 55, 0.95);
    }
</style>

@php
    $record = $getRecord();
    
    $colorMap = [
        'Disponible' => 'text-green-700 bg-green-100 ring-green-600/30 dark:text-green-400 dark:bg-green-900/30',
        'Reservada' => 'text-yellow-700 bg-yellow-100 ring-yellow-600/30 dark:text-yellow-400 dark:bg-yellow-900/30',
        'Ocupada' => 'text-red-700 bg-red-100 ring-red-600/30 dark:text-red-400 dark:bg-red-900/30',
        'Mantenimiento' => 'text-orange-700 bg-orange-100 ring-orange-600/30 dark:text-orange-400 dark:bg-orange-900/30',
        'Inactiva' => 'text-gray-700 bg-gray-100 ring-gray-600/30 dark:text-gray-400 dark:bg-gray-900/30',
    ];
    $estadoColor = $colorMap[$record->estado] ?? 'text-gray-700 bg-gray-100 ring-gray-600/30';
    $isActivo = $record->activo;
@endphp

<div class="hotel-card-container">
    <div class="wave-bg"></div>
    
    <div class="hotel-card-content">
        <div class="hotel-card-header flex justify-between items-start">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/50 rounded-lg shadow-sm border border-amber-100 dark:border-amber-800">
                    <svg class="w-6 h-6 text-amber-700 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Habitación {{ $record->numero }}</h3>
                    <p class="text-sm font-medium text-amber-700 dark:text-amber-400">{{ $record->tipo }}</p>
                </div>
            </div>
            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $estadoColor }} shadow-sm">
                {{ $record->estado }}
            </span>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700 shadow-sm mb-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Capacidad: {{ $record->capacidad }}
                </div>
                <div class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    $ {{ number_format($record->precio_noche, 2) }}
                </div>
            </div>
        </div>

        @if($record->descripcion)
        <div class="text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-3 mb-2 line-clamp-2 mt-auto">
            {{ $record->descripcion }}
        </div>
        @endif
        
        @if(!$isActivo)
        <div class="mt-3 flex items-center text-xs font-semibold text-red-500 bg-red-50 dark:bg-red-900/20 p-2 rounded-md">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Habitación temporalmente inactiva
        </div>
        @endif
    </div>
</div>
