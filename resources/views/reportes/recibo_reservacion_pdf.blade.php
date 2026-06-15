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
        body {
            font-family: 'Courier New', monospace;
            color: #000;
            font-size: 10px;
            line-height: 1.3;
            width: 80mm;
            margin: 0 auto;
        }
        .container {
            width: 80mm;
            margin: 0;
            padding: 5mm;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 3mm;
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
        .factura-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 3mm 0;
            border: 1px solid #000;
            padding: 2mm;
        }
        .factura-numero {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            margin: 2mm 0 1mm 0;
        }
        .cuf-box {
            border: 1px solid #000;
            padding: 2mm;
            margin: 2mm 0;
            background-color: #f9f9f9;
            text-align: center;
        }
        .cuf-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .cuf-value {
            font-size: 7px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            margin-top: 1mm;
            letter-spacing: 1px;
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
        }
        .label {
            font-weight: bold;
            width: 35%;
            flex-shrink: 0;
        }
        .value {
            flex: 1;
            word-wrap: break-word;
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
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }
        .totales {
            margin: 2mm 0;
            padding: 1mm;
            border: 1px solid #000;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin-bottom: 1mm;
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 11px;
            border-top: 1px solid #000;
            padding-top: 1mm;
            margin-top: 1mm;
        }
        .qr-box {
            border: 1px solid #000;
            padding: 3mm;
            margin: 2mm 0;
            text-align: center;
            min-height: 25mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
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
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 3mm;
            }
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- ENCABEZADO HOTEL -->
    <div class="header">
        <div class="hotel-name">HOTEL CLUB CAMPESTRE<br>LA MANSIÓN</div>
        <div class="hotel-info">
            <div>NIT: 000000000</div>
            <div>Sorata, La Paz - Bolivia</div>
            <div>Teléfono: +591 2 XXX XXXX</div>
            <div>Email: info@hotellamansion.bo</div>
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
        {{ now()->format('d/m/Y H:i') }}
    </div>

    <!-- INFORMACIÓN DE EMISIÓN -->
    <div class="section">
        <div class="receipt-info">
            <div class="receipt-info-row">
                <span class="bold">Sucursal:</span> 0001 | 
                <span class="bold">Punto Venta:</span> 0000001
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <!-- DATOS DEL CLIENTE -->
    <div class="section">
        <div class="section-title">Cliente</div>
        <div class="row">
            <span class="label">Nombre:</span>
            <span class="value">{{ $reservacion->huesped->nombres }} {{ $reservacion->huesped->apellido_paterno }} {{ $reservacion->huesped->apellido_materno ?? '' }}</span>
        </div>
        <div class="row">
            <span class="label">CI/NIT:</span>
            <span class="value">{{ $reservacion->huesped->numero_documento }}</span>
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
            <span class="value">Nº {{ $reservacion->habitacion->numero }} ({{ $reservacion->habitacion->tipo }})</span>
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
            <span class="value">{{ $reservacion->cantidad_personas }}</span>
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
            <span class="value">{{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->diffInDays(\Carbon\Carbon::parse($reservacion->fecha_salida)) ?? 1 }} noche(s)</span>
        </div>
        <div class="row">
            <span class="label">Precio Unit.:</span>
            <span class="value">Bs. {{ number_format($reservacion->total / (max(1, \Carbon\Carbon::parse($reservacion->fecha_entrada)->diffInDays(\Carbon\Carbon::parse($reservacion->fecha_salida)))), 2) }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- TOTALES -->
    <div class="totales">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Bs. {{ number_format($reservacion->total, 2) }}</span>
        </div>
        <div class="total-row">
            <span>Descuento:</span>
            <span>Bs. 0.00</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL A PAGAR:</span>
            <span>Bs. {{ number_format($reservacion->total, 2) }}</span>
        </div>
    </div>

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

    <!-- QR DE VERIFICACIÓN -->
    <div class="qr-box">
        <div class="qr-label">QR DE VERIFICACIÓN</div>
        <div class="qr-placeholder">◼ ◼</div>
        <div style="font-size: 7px; margin-top: 1mm; text-align: center;">
            NIT: 000000000 | Fact: {{ $numeroFactura ?? '000001' }}
        </div>
    </div>

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        <div class="footer-text">
            ═══════════════════════════════
        </div>
        <div class="footer-text">
            <strong>FACTURA ACADÉMICA</strong>
        </div>
        <div class="footer-text">
            Generada para fines académicos.
        </div>
        <div class="footer-text">
            No válida para crédito fiscal.
        </div>
        <div class="footer-text">
            No es un comprobante fiscal oficial.
        </div>
        <div class="footer-text">
            ═══════════════════════════════
        </div>
        <div class="footer-text" style="margin-top: 2mm;">
            Gracias por su preferencia.
        </div>
        <div class="footer-text" style="margin-top: 1mm; font-size: 6px;">
            Sistema HOT - Hotel Club Campestre La Mansión
        </div>
    </div>

</div>

</body>
</html>

