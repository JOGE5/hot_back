<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            color: #222;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #9a7b38;
            margin-bottom: 16px;
            padding-bottom: 10px;
            text-align: center;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 4px;
            text-transform: uppercase;
        }

        .meta {
            margin-bottom: 14px;
        }

        .meta p {
            margin: 2px 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f2ead7;
            font-size: 9px;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background: #fafafa;
        }

        .footer {
            bottom: -20px;
            color: #777;
            font-size: 8px;
            left: 0;
            position: fixed;
            right: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <div>Hotel Club Campestre La Mansión</div>
    </div>

    <div class="meta">
        <p><strong>Fecha de generación:</strong> {{ $fechaGeneracion }}</p>
        <p><strong>Usuario:</strong> {{ $usuarioGenerador }}</p>
        <p><strong>Columnas seleccionadas:</strong> {{ implode(', ', array_values($columnas)) }}</p>
        <p><strong>Total de registros:</strong> {{ $filas->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($columnas as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($filas as $fila)
                <tr>
                    @foreach(array_keys($columnas) as $columna)
                        <td>{{ $fila[$columna] ?? '' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columnas) }}">No hay registros para los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Reporte generado por el sistema administrativo del Hotel Club Campestre La Mansión
    </div>
</body>
</html>
