<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Reservación</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .hotel-name {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .receipt-title {
            font-size: 16px;
            color: #d6a84f;
            margin: 0;
            font-weight: bold;
        }
        .date-emission {
            font-size: 10px;
            color: #6b7280;
            margin-top: 10px;
            text-align: right;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f3f4f6;
            padding: 5px 10px;
            border-left: 4px solid #1f2937;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 6px 10px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 140px;
            color: #4b5563;
        }
        .value {
            color: #111827;
        }
        .grid-table td {
            width: 50%;
        }
        .total-box {
            margin-top: 30px;
            border-top: 2px solid #e5e7eb;
            padding-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
        }
        .total-label {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            padding-right: 20px;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #16a34a;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="hotel-name">Hotel Club Campestre La Mansión</h1>
        <p class="receipt-title">Recibo de Reservación</p>
    </div>

    <div class="date-emission">
        Fecha de emisión: {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="section">
        <div class="section-title">Información de la Estadía</div>
        <table class="grid-table">
            <tr>
                <td>
                    <table>
                        <tr><td class="label">Código Check-in:</td><td class="value"><strong>{{ $reservacion->codigo_checkin }}</strong></td></tr>
                        @if($reservacion->codigo_checkout)
                            <tr><td class="label">Código Check-out:</td><td class="value"><strong>{{ $reservacion->codigo_checkout }}</strong></td></tr>
                        @endif
                        <tr><td class="label">Estado Reserva:</td><td class="value"><span class="status-badge">{{ $reservacion->estado_reservacion }}</span></td></tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr><td class="label">Fecha Entrada:</td><td class="value">{{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y') }}</td></tr>
                        <tr><td class="label">Fecha Salida:</td><td class="value">{{ \Carbon\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y') }}</td></tr>
                        <tr><td class="label">Huéspedes:</td><td class="value">{{ $reservacion->cantidad_personas }} {{ $reservacion->cantidad_personas == 1 ? 'persona' : 'personas' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Datos del Huésped</div>
        <table>
            <tr><td class="label">Nombre completo:</td><td class="value">{{ $reservacion->huesped->nombres }} {{ $reservacion->huesped->apellido_paterno }} {{ $reservacion->huesped->apellido_materno }}</td></tr>
            <tr><td class="label">Documento:</td><td class="value">{{ $reservacion->huesped->numero_documento }}</td></tr>
            <tr><td class="label">Correo electrónico:</td><td class="value">{{ $reservacion->huesped->correo_electronico ?? 'No especificado' }}</td></tr>
            <tr><td class="label">Teléfono:</td><td class="value">{{ $reservacion->huesped->telefono ?? 'No especificado' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalle de Habitación</div>
        <table>
            <tr><td class="label">Habitación:</td><td class="value">Número {{ $reservacion->habitacion->numero }}</td></tr>
            <tr><td class="label">Tipo:</td><td class="value">{{ $reservacion->habitacion->tipo }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Información de Pago</div>
        <table>
            <tr><td class="label">Método de pago:</td><td class="value">{{ $reservacion->metodo_pago ?? 'No especificado' }}</td></tr>
            <tr><td class="label">Estado del pago:</td><td class="value"><span class="status-badge">{{ $reservacion->estado_pago }}</span></td></tr>
        </table>
    </div>

    <div class="total-box">
        <table style="width: 100%;">
            <tr>
                <td class="total-label">TOTAL PAGADO:</td>
                <td class="total-amount" style="width: 150px;">Bs. {{ number_format($reservacion->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 50px; text-align: center; color: #6b7280; font-size: 10px;">
        <p>Este documento certifica el pago y la reservación en Hotel Club Campestre La Mansión.</p>
        <p>Gracias por su preferencia.</p>
    </div>

</body>
</html>
