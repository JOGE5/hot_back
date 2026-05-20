<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de acceso - Hotel La Mansión</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #374151;
            font-family: Arial, Helvetica, sans-serif;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            overflow: hidden;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .header {
            padding: 28px 36px;
            text-align: center;
            background-color: #1f2937;
        }

        .header h1 {
            margin: 0;
            color: #d6a84f;
            font-size: 22px;
            font-weight: 700;
        }

        .header p {
            margin: 6px 0 0;
            color: #d1d5db;
            font-size: 13px;
        }

        .body {
            padding: 32px 36px;
        }

        .greeting {
            margin: 0 0 12px;
            color: #111827;
            font-size: 16px;
            font-weight: 700;
        }

        .text {
            margin: 0 0 24px;
            color: #4b5563;
            font-size: 14px;
            line-height: 1.7;
        }

        .info-box {
            margin-bottom: 24px;
            padding: 20px 24px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .row {
            padding: 9px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .row:last-child {
            border-bottom: none;
        }

        .label {
            display: block;
            margin-bottom: 3px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .value {
            color: #111827;
            font-size: 15px;
            font-weight: 600;
            word-break: break-word;
        }

        .password {
            display: inline-block;
            padding: 8px 12px;
            color: #d6a84f;
            background-color: #111827;
            border-radius: 6px;
            font-family: Consolas, Monaco, monospace;
            letter-spacing: 0.5px;
        }

        .button {
            display: inline-block;
            padding: 12px 18px;
            color: #ffffff !important;
            background-color: #1f2937;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        .notice {
            margin: 24px 0 0;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
        }

        .footer {
            padding: 22px 36px;
            text-align: center;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            margin: 4px 0;
            color: #9ca3af;
            font-size: 12px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Hotel La Mansión</h1>
            <p>Credenciales de acceso</p>
        </div>

        <div class="body">
            <p class="greeting">Hola, {{ $user->name }}.</p>

            <p class="text">
                Se ha creado una cuenta para usted en el sistema del Hotel La Mansión.
                Use las siguientes credenciales para ingresar.
            </p>

            <div class="info-box">
                <div class="row">
                    <span class="label">Nombre</span>
                    <span class="value">{{ $user->name }}</span>
                </div>
                <div class="row">
                    <span class="label">Correo</span>
                    <span class="value">{{ $user->email }}</span>
                </div>
                <div class="row">
                    <span class="label">Contraseña temporal</span>
                    <span class="value password">{{ $passwordTemporal }}</span>
                </div>
                <div class="row">
                    <span class="label">Rol</span>
                    <span class="value">{{ $user->role?->nombre ?? 'Sin rol' }}</span>
                </div>
                <div class="row">
                    <span class="label">URL de acceso</span>
                    <span class="value">{{ $urlAcceso }}</span>
                </div>
            </div>

            <a href="{{ $urlAcceso }}" class="button">Ingresar al sistema</a>

            <p class="notice">
                Por seguridad, cambie esta contraseña después de iniciar sesión por primera vez.
                Si no esperaba este correo, comuníquese con administración.
            </p>
        </div>

        <div class="footer">
            <p><strong>Hotel La Mansión</strong></p>
            <p>Este es un correo generado automáticamente. Por favor no responda a este mensaje.</p>
        </div>
    </div>
</body>
</html>
