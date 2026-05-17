<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Reservación - Hotel La Mansión</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #374151;
        }
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .header {
            background-color: #1f2937;
            padding: 32px 40px 24px;
            text-align: center;
        }
        .header h1 {
            color: #d6a84f;
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            color: #9ca3af;
            font-size: 13px;
            margin: 0;
        }
        .body {
            padding: 36px 40px;
        }
        .saludo {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }
        .intro {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 28px;
        }
        .info-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 24px 28px;
            margin-bottom: 24px;
        }
        .info-box h2 {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.8px;
            margin: 0 0 16px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 0;
            font-size: 14px;
            border-bottom: 1px dashed #f3f4f6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6b7280;
            font-weight: 500;
        }
        .info-value {
            color: #1f2937;
            font-weight: 600;
            text-align: right;
        }
        .codigo-badge {
            display: inline-block;
            background-color: #1f2937;
            color: #d6a84f;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 2px;
            padding: 6px 16px;
            border-radius: 6px;
        }
        .total-box {
            background-color: #1f2937;
            border-radius: 10px;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        .total-label {
            color: #9ca3af;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .total-amount {
            color: #4ade80;
            font-size: 22px;
            font-weight: 800;
        }
        .adjunto-aviso {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 14px 20px;
            font-size: 13px;
            color: #1d4ed8;
            margin-bottom: 28px;
            line-height: 1.6;
        }
        .footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 24px 40px;
            text-align: center;
        }
        .footer p {
            font-size: 12px;
            color: #9ca3af;
            margin: 4px 0;
            line-height: 1.6;
        }
        .footer .brand {
            font-weight: 700;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <div class="header">
            <h1>Hotel Club Campestre La Mansión</h1>
            <p>Recibo de Reservación</p>
        </div>

        <div class="body">

            <p class="saludo">
                Estimado/a {{ $reservacion->huesped->nombres }} {{ $reservacion->huesped->apellido_paterno }},
            </p>

            <p class="intro">
                Nos complace informarle que su pago ha sido confirmado exitosamente. Adjunto a este correo
                encontrará su recibo oficial de reservación en formato PDF para su referencia.<br><br>
                A continuación le presentamos un resumen de su estadía:
            </p>

            {{-- Código de Check-in --}}
            <div class="info-box">
                <h2>Código de Check-in</h2>
                <div style="text-align: center; padding: 8px 0;">
                    <span class="codigo-badge">{{ $reservacion->codigo_checkin }}</span>
                    <p style="font-size: 12px; color: #6b7280; margin: 10px 0 0;">
                        Presente este código al momento de su llegada al hotel.
                    </p>
                </div>
            </div>

            {{-- Datos de la estadía --}}
            <div class="info-box">
                <h2>Detalles de la Reservación</h2>
                <div class="info-row">
                    <span class="info-label">Huésped</span>
                    <span class="info-value">
                        {{ $reservacion->huesped->nombres }}
                        {{ $reservacion->huesped->apellido_paterno }}
                        {{ $reservacion->huesped->apellido_materno }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Habitación</span>
                    <span class="info-value">
                        Número {{ $reservacion->habitacion->numero }} — {{ $reservacion->habitacion->tipo }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de entrada</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de salida</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Huéspedes</span>
                    <span class="info-value">
                        {{ $reservacion->cantidad_personas }}
                        {{ $reservacion->cantidad_personas == 1 ? 'persona' : 'personas' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Método de pago</span>
                    <span class="info-value">{{ $reservacion->metodo_pago ?? 'No especificado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado del pago</span>
                    <span class="info-value" style="color: #16a34a;">Confirmado</span>
                </div>
            </div>

            {{-- Total --}}
            <div class="total-box">
                <span class="total-label">Total pagado</span>
                <span class="total-amount">Bs. {{ number_format($reservacion->total, 2) }}</span>
            </div>

            {{-- Aviso del adjunto --}}
            <div class="adjunto-aviso">
                📄 <strong>Recibo adjunto:</strong> Se ha incluido el recibo oficial en formato PDF adjunto a este
                correo. Puede descargarlo y guardarlo como comprobante de su reservación.
            </div>

        </div>

        <div class="footer">
            <p class="brand">Hotel Club Campestre La Mansión</p>
            <p>Este es un correo generado automáticamente. Por favor no responda a este mensaje.</p>
            <p>Si tiene alguna consulta, contáctenos directamente en recepción.</p>
        </div>

    </div>
</body>
</html>
