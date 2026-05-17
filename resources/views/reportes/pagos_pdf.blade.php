<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f3f4f6; font-size: 11px; text-transform: uppercase; }
        h2 { text-align: center; color: #374151; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>Reporte de Pagos - Hotel La Mansión</h2>
    <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Código Check-in</th>
                <th>Huésped</th>
                <th>Documento</th>
                <th>Hab.</th>
                <th>Método de Pago</th>
                <th>Monto (Bs.)</th>
                <th>Estado</th>
                <th>Fecha de Pago</th>
                <th>Registrado Por</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
                @php
                    $huesped = $pago->reservacion?->huesped;
                    $nombreHuesped = $huesped ? "{$huesped->nombres} {$huesped->apellido_paterno}" : 'N/A';
                    $documento = $huesped ? $huesped->numero_documento : 'N/A';
                    $habitacion = $pago->reservacion?->habitacion;
                    $numHabitacion = $habitacion ? $habitacion->numero : 'N/A';
                    $registradoPor = $pago->registradoPor ? "{$pago->registradoPor->nombres} {$pago->registradoPor->apellido_paterno}" : 'N/A';
                @endphp
                <tr>
                    <td>{{ $pago->reservacion?->codigo_checkin ?? 'N/A' }}</td>
                    <td>{{ $nombreHuesped }}</td>
                    <td>{{ $documento }}</td>
                    <td>{{ $numHabitacion }}</td>
                    <td>{{ $pago->metodo_pago }}</td>
                    <td class="text-right">{{ number_format($pago->monto, 2) }}</td>
                    <td>{{ $pago->estado_pago }}</td>
                    <td>{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>{{ $registradoPor }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL GENERAL</th>
                <th class="text-right">Bs. {{ number_format($pagos->sum('monto'), 2) }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
