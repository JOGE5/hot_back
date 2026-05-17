<x-filament-panels::page>
    <style>
        .check-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .check-card {
            background: #18181b;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            align-self: start;
        }

        .check-title {
            color: #ffffff;
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .check-subtitle {
            font-size: 13px;
            color: #a1a1aa;
            margin-bottom: 20px;
        }

        .check-form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }

        .check-label {
            font-size: 13px;
            font-weight: 700;
            color: #e5e7eb;
        }

        .check-input-row {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .check-input {
            flex: 1;
            height: 40px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: #09090b;
            color: #ffffff;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.05em;
            outline: none;
            transition: all 0.2s;
        }

        .check-input.uppercase {
            text-transform: uppercase;
        }

        .check-input:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
        }

        .check-button {
            height: 40px;
            border-radius: 8px;
            background: #16a34a;
            color: white;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 800;
            transition: 0.2s ease;
            white-space: nowrap;
            border: none;
            cursor: pointer;
        }

        .check-button.danger {
            background: #dc2626;
        }

        .check-button:hover {
            background: #22c55e;
        }

        .check-button.danger:hover {
            background: #ef4444;
        }

        .check-error {
            margin-top: 12px;
            border-radius: 8px;
            border: 1px solid rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 600;
        }

        .check-result {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .check-result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .check-result-title {
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
        }

        .check-code {
            color: #86efac;
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 800;
        }

        .check-code.checkout {
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .check-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .check-item-label {
            color: #71717a;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .check-item-value {
            color: #e5e7eb;
            font-size: 13px;
            font-weight: 600;
        }

        .check-item-value strong {
            color: #ffffff;
            font-weight: 800;
        }

        .check-footer {
            margin-top: 16px;
        }

        @media (max-width: 480px) {
            .check-input-row {
                flex-direction: column;
                align-items: stretch;
            }
            .check-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="check-container">
        
        <!-- Bloque Check-in -->
        <div class="check-card">
            <h2 class="check-title">Check-in</h2>
            <p class="check-subtitle">
                Ingrese el código de check-in para registrar la entrada.
            </p>

            <div class="check-form-group">
                <label class="check-label">Código de check-in</label>
                <div class="check-input-row">
                    <input
                        type="text"
                        wire:model.defer="codigo_checkin"
                        wire:keydown.enter="validarCodigo"
                        placeholder="EJ: CHK-A1B2C3"
                        class="check-input uppercase"
                    />
                    <button
                        type="button"
                        wire:click="validarCodigo"
                        class="check-button"
                    >
                        Validar
                    </button>
                </div>
            </div>

            @if($error_message)
                <div class="check-error">
                    {{ $error_message }}
                </div>
            @endif

            @if($reservacion)
                <div class="check-result">
                    <div class="check-result-header">
                        <div class="check-result-title">Reserva Validada</div>
                        <div class="check-code">{{ $reservacion->codigo_checkin }}</div>
                    </div>

                    <div class="check-grid">
                        <div>
                            <div class="check-item-label">Huésped</div>
                            <div class="check-item-value">
                                <strong>{{ $reservacion->huesped->nombres }} {{ $reservacion->huesped->apellido_paterno }}</strong><br>
                                <span style="font-size: 11px; color: #a1a1aa;">Doc: {{ $reservacion->huesped->numero_documento }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="check-item-label">Habitación</div>
                            <div class="check-item-value">
                                <strong>Hab. {{ $reservacion->habitacion->numero }}</strong><br>
                                <span style="font-size: 11px; color: #a1a1aa;">{{ $reservacion->habitacion->tipo }} ({{ $reservacion->cantidad_personas }} p.)</span>
                            </div>
                        </div>
                        
                        <div style="grid-column: 1 / -1;">
                            <div class="check-item-label">Fechas</div>
                            <div class="check-item-value">
                                {{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="check-footer">
                        <button
                            type="button"
                            wire:click="confirmarCheckIn"
                            class="check-button"
                            style="width: 100%; text-align: center; font-size: 14px;"
                        >
                            Confirmar check-in
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Bloque Check-out -->
        <div class="check-card">
            <h2 class="check-title">Check-out</h2>
            <p class="check-subtitle">
                Busque la estadía activa para registrar la salida.
            </p>

            <div class="check-form-group">
                <label class="check-label">Buscar estadía</label>
                <div class="check-input-row">
                    <input
                        type="text"
                        wire:model.defer="busqueda_checkout"
                        wire:keydown.enter="buscarCheckOut"
                        placeholder="Código, doc, hab..."
                        class="check-input uppercase"
                    />
                    <button
                        type="button"
                        wire:click="buscarCheckOut"
                        class="check-button"
                    >
                        Buscar
                    </button>
                </div>
            </div>

            @if($error_checkout)
                <div class="check-error">
                    {{ $error_checkout }}
                </div>
            @endif

            @if($reservacion_checkout)
                <div class="check-result">
                    <div class="check-result-header">
                        <div class="check-result-title">Estadía Encontrada</div>
                        <div class="check-code checkout">{{ $reservacion_checkout->codigo_checkout ?? 'N/A' }}</div>
                    </div>

                    <div class="check-grid">
                        <div>
                            <div class="check-item-label">Huésped</div>
                            <div class="check-item-value">
                                <strong>{{ $reservacion_checkout->huesped->nombres }} {{ $reservacion_checkout->huesped->apellido_paterno }}</strong><br>
                                <span style="font-size: 11px; color: #a1a1aa;">Doc: {{ $reservacion_checkout->huesped->numero_documento }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="check-item-label">Habitación</div>
                            <div class="check-item-value">
                                <strong>Hab. {{ $reservacion_checkout->habitacion->numero }}</strong><br>
                                <span style="font-size: 11px; color: #a1a1aa;">{{ $reservacion_checkout->habitacion->tipo }}</span>
                            </div>
                        </div>

                        <div style="grid-column: 1 / -1;">
                            <div class="check-item-label">Ingreso registrado</div>
                            <div class="check-item-value">
                                {{ \Carbon\Carbon::parse($reservacion_checkout->checkin_at)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="check-footer">
                        <button
                            type="button"
                            wire:click="confirmarCheckOut"
                            class="check-button danger"
                            style="width: 100%; text-align: center; font-size: 14px;"
                        >
                            Confirmar check-out
                        </button>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>