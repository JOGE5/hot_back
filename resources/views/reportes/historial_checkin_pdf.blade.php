<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Check</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #d6a84f;
            padding-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 14px;
            color: #d6a84f;
            margin-top: 5px;
        }
        .filters {
            margin-bottom: 15px;
            font-size: 9px;
            background: #f9fafb;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #1f2937;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .status {
            font-weight: bold;
        }
        .status-en-estadia { color: #2563eb; }
        .status-finalizada { color: #6b7280; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="title">Hotel La Mansión</h1>
        <p class="subtitle">Historial de Check</p>
        <p style="font-size: 9px; margin-top: 5px; color: #6b7280;">Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="filters">
        <strong>Filtros aplicados:</strong><br>
        Buscar: {{ $filtros['Buscar'] ?? 'Todos' }} |
        Estado: {{ $filtros['Estado'] ?? 'Todos' }} |
        Pago: {{ $filtros['Método Pago'] ?? 'Todos' }} <br>
        <strong>IN:</strong>
        Desde: {{ $filtros['IN Desde'] ? \Carbon\Carbon::parse($filtros['IN Desde'])->format('d/m/Y') : 'Inicio' }} |
        Hasta: {{ $filtros['IN Hasta'] ? \Carbon\Carbon::parse($filtros['IN Hasta'])->format('d/m/Y') : 'Fin' }} |
        <strong>OUT:</strong>
        Desde: {{ $filtros['OUT Desde'] ? \Carbon\Carbon::parse($filtros['OUT Desde'])->format('d/m/Y') : 'Inicio' }} |
        Hasta: {{ $filtros['OUT Hasta'] ? \Carbon\Carbon::parse($filtros['OUT Hasta'])->format('d/m/Y') : 'Fin' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Código IN</th>
                <th>IN</th>
                <th>Usuario IN</th>
                <th>Código OUT</th>
                <th>OUT</th>
                <th>Usuario OUT</th>
                <th>Huésped</th>
                <th>Habitación</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Total (Bs.)</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservaciones as $reservacion)
                <tr>
                    <td><strong>{{ $reservacion->codigo_checkin }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($reservacion->checkin_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $reservacion->checkinUser ? explode(' ', $reservacion->checkinUser->name)[0] : 'N/A' }}</td>
                    
                    <td style="color: #ef4444;"><strong>{{ $reservacion->codigo_checkout ?? '-' }}</strong></td>
                    <td>{{ $reservacion->checkout_at ? \Carbon\Carbon::parse($reservacion->checkout_at)->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $reservacion->checkoutUser ? explode(' ', $reservacion->checkoutUser->name)[0] : '-' }}</td>
                    
                    <td>{{ $reservacion->huesped->nombres }} {{ $reservacion->huesped->apellido_paterno }} <br> <span style="font-size: 8px; color: #6b7280;">{{ $reservacion->huesped->numero_documento }}</span></td>
                    <td>Hab. {{ $reservacion->habitacion->numero }} <br> <span style="font-size: 8px; color: #6b7280;">{{ $reservacion->habitacion->tipo }}</span></td>
                    
                    <td>{{ \Carbon\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y') }}</td>
                    
                    <td class="text-right">{{ number_format($reservacion->total, 2) }} <br> <span style="font-size: 8px; color: #6b7280;">{{ $reservacion->metodo_pago }}</span></td>
                    <td class="status {{ $reservacion->estado_reservacion === 'En estadía' ? 'status-en-estadia' : 'status-finalizada' }}">
                        {{ strtoupper($reservacion->estado_reservacion) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
