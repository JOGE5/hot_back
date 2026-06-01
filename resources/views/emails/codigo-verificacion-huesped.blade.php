<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de verificación - Hotel La Mansión</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #374151;
            font-family: Arial, Helvetica, sans-serif;
        }

        .wrapper {
            max-width: 560px;
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
            color: #f59e0b;
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
            text-align: center;
        }

        .greeting {
            margin: 0 0 12px;
            color: #111827;
            font-size: 16px;
            font-weight: 700;
        }

        .text {
            margin: 0 0 22px;
            color: #4b5563;
            font-size: 14px;
            line-height: 1.7;
        }

        .code {
            display: inline-block;
            margin: 6px 0 22px;
            padding: 14px 22px;
            color: #f59e0b;
            background-color: #111827;
            border-radius: 8px;
            font-family: Consolas, Monaco, monospace;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 6px;
        }

        .notice {
            margin: 0;
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
            <p>Código de verificación</p>
        </div>

        <div class="body">
            <p class="greeting">Hola, {{ $user->name }}.</p>

            <p class="text">
                Usa este código de 6 dígitos para ingresar al panel de huésped.
            </p>

            <div class="code">{{ $code }}</div>

            <p class="notice">
                Vigencia: {{ $vigenciaMinutos }} minutos.<br>
                No compartas este código con nadie. Si no solicitaste este acceso, ignora este correo.
            </p>
        </div>

        <div class="footer">
            <p><strong>Hotel La Mansión</strong></p>
            <p>Este es un correo generado automáticamente. Por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>
