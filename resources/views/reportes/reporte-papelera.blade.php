<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de bajas lógicas</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1f2937;
            font-size: 10px;
            margin: 0;
        }
        .header {
            border-bottom: 2px solid #92400e;
            margin-bottom: 18px;
            padding-bottom: 10px;
        }
        h1 {
            font-size: 20px;
            margin: 0 0 6px;
            text-transform: uppercase;
        }
        .meta {
            margin-bottom: 14px;
            line-height: 1.6;
        }
        .notice {
            background: #fffbeb;
            border: 1px solid #fde68a;
            padding: 10px;
            margin-bottom: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border-bottom: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            font-size: 9px;
            text-transform: uppercase;
        }
        .empty {
            color: #6b7280;
            padding: 18px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de bajas lógicas</h1>
        <div>{{ $modulo }}</div>
    </div>

    <div class="meta">
        <div><strong>Fecha y hora:</strong> {{ $fechaGeneracion }}</div>
        <div><strong>Solicitado por:</strong> {{ $usuarioSolicitante }}</div>
        <div><strong>Cantidad de registros:</strong> {{ $cantidad }}</div>
    </div>

    <div class="notice">
        Este reporte respalda los registros dados de baja lógicamente en el sistema.
    </div>

    @if ($registros->isEmpty())
        <div class="empty">No hay registros en la papelera para reportar.</div>
    @else
        <table>
            <thead>
                <tr>
                    @foreach ($columnas as $columna)
                        <th>{{ $columna }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($registros as $registro)
                    <tr>
                        @foreach ($registro as $valor)
                            <td>{{ $valor }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
