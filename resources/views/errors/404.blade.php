@php
    $homeUrl = auth()->check() ? url('/admin') : url('/');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>No encontramos esta página | Hotel La Mansión</title>
    <style>
        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            color: #f8efe0;
            background:
                linear-gradient(135deg, rgba(18, 38, 30, .94), rgba(62, 42, 25, .9)),
                radial-gradient(circle at 20% 10%, rgba(211, 178, 101, .28), transparent 32%),
                #14241d;
        }

        .card {
            width: min(100%, 620px);
            padding: 38px 32px 34px;
            text-align: center;
            background: #fbf3e4;
            color: #25372d;
            border: 1px solid rgba(199, 163, 83, .58);
            border-radius: 18px;
            box-shadow: 0 28px 80px rgba(0, 0, 0, .34);
        }

        .brand {
            margin-bottom: 20px;
            color: #9a7332;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        .illustration {
            width: 142px;
            height: 142px;
            margin: 0 auto 30px;
            animation: float 4s ease-in-out infinite;
        }

        .needle {
            transform-origin: 80px 80px;
            animation: search 4s ease-in-out infinite;
        }

        h1 {
            margin: 0 0 14px;
            color: #193f31;
            font-size: clamp(28px, 6vw, 40px);
            line-height: 1.1;
        }

        p {
            margin: 0 auto 28px;
            max-width: 470px;
            color: #665d50;
            font-size: 16px;
            line-height: 1.7;
        }

        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 24px;
            color: #fffaf0;
            background: #183f31;
            border: 1px solid #c7a353;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 10px 24px rgba(24, 63, 49, .25);
        }

        a:hover { background: #102d23; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes search {
            0%, 100% { transform: rotate(-18deg); }
            50% { transform: rotate(18deg); }
        }
    </style>
</head>
<body>
    <main class="card" aria-labelledby="error-title">
        <div class="brand">Hotel Club Campestre La Mansión</div>

        <svg class="illustration" viewBox="0 0 160 160" role="img" aria-label="Brújula buscando una ruta">
            <circle cx="80" cy="80" r="54" fill="#f4e6c9" stroke="#c7a353" stroke-width="8"/>
            <circle cx="80" cy="80" r="38" fill="#fbf3e4" stroke="#183f31" stroke-width="3"/>
            <path d="M80 24v14M80 122v14M24 80h14M122 80h14" stroke="#9a7332" stroke-width="5" stroke-linecap="round"/>
            <g class="needle">
                <path d="M80 42l13 38-13 38-13-38z" fill="#183f31"/>
                <path d="M80 42l13 38H80z" fill="#d1ad61"/>
            </g>
            <circle cx="80" cy="80" r="6" fill="#6b4428"/>
        </svg>

        <h1 id="error-title">No encontramos esta página</h1>
        <p>La dirección que buscas no existe o fue movida. Puedes volver al inicio para continuar navegando.</p>
        <a href="{{ $homeUrl }}">Volver al inicio</a>
    </main>
</body>
</html>
