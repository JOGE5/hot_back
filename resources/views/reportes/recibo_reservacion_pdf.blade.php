<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de Reservación</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        @page {
            margin: 0;
        }
        html,
        body {
            margin: 0;
            padding: 0;
            width: 80mm;
        }
        body {
            font-family: 'Courier New', monospace;
            color: #000;
            font-size: 10px;
            line-height: 1.25;
        }
        .ticket {
            width: 80mm;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            overflow: visible;
        }
        .inner {
            width: 70mm;
            max-width: 70mm;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 3mm 0 3mm 0;
            margin-bottom: 3mm;
        }
        .hotel-name {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .hotel-info {
            font-size: 8px;
            line-height: 1.2;
            margin-top: 1mm;
        }
        .factura-title,
        .cuf-box,
        .qr-box,
        .totales,
        .header {
            width: 70mm;
            max-width: 70mm;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
            border: 0.5px solid #000;
        }
        .section {
            width: 70mm;
            max-width: 70mm;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
        }
        .factura-title {
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            margin: 2mm auto;
            padding: 4mm 0;
            border: 0.5px solid #000;
        }
        .factura-numero {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            margin: 2mm 0 1mm 0;
        }
        .logo {
            display: block;
            margin: 0 auto 3mm auto;
            max-width: 45mm;
            height: auto;
        }
        .cuf-box {
            padding: 2mm;
            margin: 2mm auto;
            background-color: #f9f9f9;
            text-align: center;
            width: 70mm;
            max-width: 70mm;
            box-sizing: border-box;
            overflow: hidden;
            border: 0.5px solid #000;
        }
        .cuf-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .cuf-value,
        .cuf-text {
            font-size: 6px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            overflow-wrap: anywhere;
            max-width: 100%;
            display: inline-block;
            box-sizing: border-box;
            margin-top: 1mm;
            letter-spacing: 0.5px;
        }
        .section {
            margin: 2mm 0;
            padding: 0;
        }
        .section-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px dashed #000;
            padding: 1mm 0;
            margin-bottom: 1mm;
        }
        .row {
            display: flex;
            font-size: 9px;
            margin-bottom: 1mm;
            width: 100%;
            box-sizing: border-box;
        }
        .label {
            font-weight: bold;
            width: 35%;
            flex-shrink: 0;
            box-sizing: border-box;
        }
        .value {
            flex: 1;
            word-wrap: break-word;
            overflow-wrap: anywhere;
            box-sizing: border-box;
        }
        .receipt-info {
            font-size: 8px;
            margin: 2mm 0;
            padding: 0;
        }
        .receipt-info-row {
            margin-bottom: 0.5mm;
        }
        .bold {
            font-weight: bold;
        }
        .divider {
            width: 70mm;
            margin: 1.5mm auto;
            border-top: 0.5px dashed #000;
            box-sizing: border-box;
        }
        .line-solid {
            width: 70mm;
            margin: 1.5mm auto;
            border-top: 0.5px solid #000;
            box-sizing: border-box;
        }
        .totales {
            margin: 2mm auto;
            padding: 2mm;
            border: 0.5px solid #000;
            width: 70mm;
            max-width: 70mm;
            box-sizing: border-box;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin-bottom: 1mm;
            width: 100%;
            box-sizing: border-box;
        }
        .total-row span {
            display: inline-block;
            box-sizing: border-box;
            width: 50%;
            vertical-align: middle;
        }
        .total-row span.amount {
            text-align: right;
            white-space: nowrap;
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 11px;
            border-top: 0.5px solid #000;
            padding-top: 1mm;
            margin-top: 1mm;
        }
        .qr-box {
            border: 0.5px solid #000;
            padding: 2mm;
            margin: 2mm auto;
            text-align: center;
            min-height: 25mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
            width: 70mm;
            max-width: 70mm;
            box-sizing: border-box;
        }
        .qr-image {
            max-width: 60mm;
            width: 40mm;
            height: auto;
            display: block;
        }
        .qr-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }
        .qr-placeholder {
            font-size: 20px;
            color: #ccc;
        }
        .footer {
            text-align: center;
            font-size: 7px;
            margin-top: 3mm;
            padding-top: 2mm;
            border-top: 1px solid #000;
            line-height: 1.2;
        }
        .footer-text {
            margin-bottom: 1mm;
        }
        .fecha-hora {
            text-align: right;
            font-size: 8px;
            margin-bottom: 1mm;
        }
        /* Tablas seguras para ticket */
        table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            box-sizing: border-box;
        }
        td, th {
            word-break: break-word;
            overflow-wrap: anywhere;
            box-sizing: border-box;
        }
        .amount { text-align: right; white-space: nowrap; }
    </style>
</head>
<body>

<div class="ticket"><div class="inner">
    @if(!empty($logoBase64))
        <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
    @endif

    <!-- ENCABEZADO HOTEL -->
    <div class="header">
        <div class="hotel-name">HOTEL CLUB CAMPESTRE<br>LA MANSIÓN</div>
        <div class="hotel-info">
            <div>Sucursal 1</div>
            <div>NIT: 000000000</div>
            <div>Sorata, La Paz - Bolivia</div>
        </div>
    </div>

    <!-- TIPO DE FACTURA -->
    <div class="factura-title">FACTURA</div>

    <!-- NÚMERO DE FACTURA -->
    <div class="factura-numero">
        N°: {{ $numeroFactura ?? '000001' }}
    </div>

    <!-- CUF -->
    <div class="cuf-box">
        <div class="cuf-label">CUF (Código Único de Factura)</div>
        <div class="cuf-value">{{ $cuf ?? 'NO DISPONIBLE' }}</div>
    </div>

    <!-- FECHA Y HORA DE EMISIÓN -->
    <div class="fecha-hora">
        Emitido: {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="divider"></div>

    <!-- DATOS DEL CLIENTE -->
    <div class="section">
        <div class="section-title">Cliente</div>
        <div class="row">
            <span class="label">Nombre/Razón Social:</span>
            <span class="value">{{ trim($reservacion->huesped->nombres . ' ' . $reservacion->huesped->apellido_paterno . ' ' . ($reservacion->huesped->apellido_materno ?? '')) }}</span>
        </div>
        <div class="row">
            <span class="label">CI/NIT:</span>
            <span class="value">{{ $reservacion->huesped->numero_documento ?? 'No disponible' }}</span>
        </div>
        <div class="row">
            <span class="label">Teléfono:</span>
            <span class="value">{{ $reservacion->huesped->telefono ?? 'N/A' }}</span>
        </div>
        <div class="row">
            <span class="label">Email:</span>
            <span class="value">{{ $reservacion->huesped->correo_electronico ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- DATOS DE RESERVACIÓN -->
    <div class="section">
        <div class="section-title">Reservación</div>
        <div class="row">
            <span class="label">Código:</span>
            <span class="value">{{ $reservacion->codigo_checkin }}</span>
        </div>
        <div class="row">
            <span class="label">Habitación:</span>
            <span class="value">Nº {{ $reservacion->habitacion->numero }}{{ isset($reservacion->habitacion->tipo) ? ' (' . $reservacion->habitacion->tipo . ')' : '' }}</span>
        </div>
        <div class="row">
            <span class="label">Entrada:</span>
            <span class="value">{{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y') }}</span>
        </div>
        <div class="row">
            <span class="label">Salida:</span>
            <span class="value">{{ \Carbon\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y') }}</span>
        </div>
        <div class="row">
            <span class="label">Huéspedes:</span>
            <span class="value">{{ $reservacion->cantidad_personas ?? '1' }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- DETALLE DEL SERVICIO -->
    <div class="section">
        <div class="section-title">Detalle</div>
        <div class="row">
            <span class="label">Servicio:</span>
            <span class="value">Hospedaje Hotel</span>
        </div>
        <div class="row">
            <span class="label">Cantidad:</span>
            <span class="value">{{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->diffInDays(\Carbon\Carbon::parse($reservacion->fecha_salida)) ?: 1 }} noche(s)</span>
        </div>
        <div class="row">
            <span class="label">Precio Unit.:</span>
            <span class="value">Bs. {{ number_format($reservacion->total / max(1, \Carbon\Carbon::parse($reservacion->fecha_entrada)->diffInDays(\Carbon\Carbon::parse($reservacion->fecha_salida))), 2) }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- TOTALES -->
    <div class="totales">
        <div class="total-row">
            <span>Subtotal:</span>
            <span class="amount">Bs. {{ number_format($reservacion->total, 2) }}</span>
        </div>
        <div class="total-row">
            <span>Descuento:</span>
            <span class="amount">Bs. 0.00</span>
        </div>
        <div class="total-row grand-total">
            <span>Total Bs.</span>
            <span class="amount">Bs. {{ number_format($reservacion->total, 2) }}</span>
        </div>
    </div>

    <div class="section">
        <div class="row">
            <span class="label">SON:</span>
            <span class="value">{{ $montoLiteral ?? '' }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- INFORMACIÓN DE PAGO -->
    <div class="section">
        <div class="receipt-info">
            <div class="receipt-info-row">
                <span class="bold">Método:</span> {{ $reservacion->metodo_pago ?? 'No especificado' }}
            </div>
            <div class="receipt-info-row">
                <span class="bold">Estado:</span> {{ $reservacion->estado_pago }}
            </div>
            <div class="receipt-info-row">
                <span class="bold">Monto Pagado:</span> Bs. {{ number_format($reservacion->total, 2) }}
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <!-- RESPONSABLE -->
    <div class="section">
        <div class="section-title">Responsable</div>
        <div class="row">
            <span class="label">Responsable:</span>
            <span class="value">{{ $responsable['nombre'] ?? 'Usuario no disponible' }}</span>
        </div>
        <div class="row">
            <span class="label">Rol:</span>
            <span class="value">{{ $responsable['rol'] ?? 'Sin rol' }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- QR DE VERIFICACIÓN -->
    <div class="qr-box">
        <div class="qr-label">QR de verificación</div>
        @if(!empty($qrBase64))
            <img src="{{ $qrBase64 }}" class="qr-image" alt="QR de verificación">
        @else
            <div class="qr-placeholder">QR no disponible</div>
        @endif
        <div style="font-size: 7px; margin-top: 1mm; text-align: center;">
            NIT: 000000000 | Fact: {{ $numeroFactura ?? '000001' }}
        </div>
    </div>

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        <div class="footer-text">═══════════════════════════════</div>
        <div class="footer-text"><strong>FACTURA ACADÉMICA</strong></div>
        <div class="footer-text">Generada para fines académicos.</div>
        <div class="footer-text">No válida para crédito fiscal.</div>
        <div class="footer-text">No es un comprobante fiscal oficial.</div>
        <div class="footer-text">═══════════════════════════════</div>
        <div class="footer-text" style="margin-top: 2mm;">Gracias por su preferencia.</div>
        <div class="footer-text" style="margin-top: 1mm; font-size: 6px;">Sistema HOT - Hotel Club Campestre La Mansión</div>
    </div>

</div>
</div>

</body>
</html>

