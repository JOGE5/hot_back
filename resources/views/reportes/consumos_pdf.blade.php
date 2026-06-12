<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Consumos</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
        }

        h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #374151;
        }

        .meta {
            margin-bottom: 12px;
        }

        .filters {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .filters td {
            border: 1px solid #e5e7eb;
            padding: 5px;
        }

        .filters .label {
            background: #f3f4f6;
            font-weight: bold;
            width: 15%;
        }

        table.detalle {
            width: 100%;
            border-collapse: collapse;
        }

        .detalle th,
        .detalle td {
            border: 1px solid #d1d5db;
            padding: 5px;
            vertical-align: top;
        }

        .detalle th {
            background-color: #f3f4f6;
            font-size: 9px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .total-row th {
            background: #ecfdf5;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <h2>Reporte de Consumos - Hotel La Mansión</h2>

    <div class="meta">
        <strong>Fecha de generación:</strong> {{ $fechaGeneracion }}
    </div>

    <table class="filters">
        <tbody>
            @foreach($filtros as $label => $valor)
                <tr>
                    <td class="label">{{ $label }}</td>
                    <td>{{ $valor }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="detalle">
        <thead>
            <tr>
                <th>Fecha de consumo</th>
                <th>Huésped</th>
                <th>Habitación</th>
                <th>Plato</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Registrado por</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consumos as $consumo)
                <tr>
                    <td>{{ $consumo->fecha_consumo ? \Carbon\Carbon::parse($consumo->fecha_consumo)->format('d/m/Y H:i') : 'Sin fecha' }}</td>
                    <td>{{ $consumo->huesped_nombre }}</td>
                    <td>{{ $consumo->habitacion_texto }}</td>
                    <td>{{ $consumo->plato_nombre }}</td>
                    <td class="text-right">{{ (int) $consumo->cantidad }}</td>
                    <td class="text-right">Bs. {{ number_format((float) $consumo->precio_unitario, 2) }}</td>
                    <td class="text-right">Bs. {{ number_format((float) $consumo->total, 2) }}</td>
                    <td>{{ $consumo->estado }}</td>
                    <td>{{ $consumo->registrado_por_nombre }}</td>
                    <td>{{ $consumo->observacion ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">No hay consumos para los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="6" class="text-right">TOTAL GENERAL CONFIRMADO</th>
                <th class="text-right">Bs. {{ number_format($totalGeneralConfirmado, 2) }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
