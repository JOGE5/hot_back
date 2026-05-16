<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Huéspedes</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #bda87a;
            padding-bottom: 15px;
        }
        .title {
            font-size: 24px;
            color: #4a3b32;
            margin: 0;
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 14px;
            color: #7a6a5e;
            margin: 5px 0;
            font-weight: 400;
        }
        .date {
            font-size: 10px;
            color: #999;
            text-align: right;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f7f3eb; /* Beige/dorado suave */
            color: #4a3b32; /* Marrón elegante */
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Reporte de Huéspedes</h1>
        <h2 class="subtitle">Hotel Club Campestre La Mansión</h2>
    </div>
    
    <div class="date">
        Fecha de generación: {{ date('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombres</th>
                <th>Apellido P.</th>
                <th>Apellido M.</th>
                <th>Documento</th>
                <th>Núm. Doc.</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Nacionalidad</th>
                <th>Nacimiento</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($huespedes as $huesped)
            <tr>
                <td>{{ $huesped->nombres }}</td>
                <td>{{ $huesped->apellido_paterno }}</td>
                <td>{{ $huesped->apellido_materno }}</td>
                <td>{{ $huesped->tipo_documento }}</td>
                <td>{{ $huesped->numero_documento }}</td>
                <td>{{ $huesped->telefono }}</td>
                <td>{{ $huesped->correo_electronico }}</td>
                <td>{{ $huesped->nacionalidad }}</td>
                <td>{{ $huesped->fecha_nacimiento ? $huesped->fecha_nacimiento->format('d/m/Y') : '' }}</td>
                <td>{{ $huesped->estado ? 'Activo' : 'Inactivo' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Reporte generado por el sistema administrativo del Hotel Club Campestre La Mansión
    </div>
</body>
</html>
