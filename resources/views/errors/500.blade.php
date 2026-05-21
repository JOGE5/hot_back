@php
    $homeUrl = auth()->check() ? url('/admin') : url('/');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Algo salió mal | Hotel La Mansión</title>
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

        .gear {
            transform-origin: 82px 76px;
            animation: spin 7s linear infinite;
        }

        .cloche {
            animation: breathe 3.2s ease-in-out infinite;
            transform-origin: 80px 92px;
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

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes breathe {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.035); }
        }
    </style>
</head>
<body>
    <main class="card" aria-labelledby="error-title">
        <div class="brand">Hotel Club Campestre La Mansión</div>

        <svg class="illustration" viewBox="0 0 160 160" role="img" aria-label="Campana de servicio en mantenimiento">
            <g class="gear">
                <circle cx="82" cy="76" r="24" fill="#d1ad61"/>
                <circle cx="82" cy="76" r="10" fill="#fbf3e4"/>
                <path d="M82 42v14M82 96v14M48 76h14M102 76h14M58 52l10 10M96 90l10 10M106 52l-10 10M68 90l-10 10" stroke="#8a6428" stroke-width="7" stroke-linecap="round"/>
            </g>
            <g class="cloche">
                <path d="M42 112h76" stroke="#6b4428" stroke-width="8" stroke-linecap="round"/>
                <path d="M52 108c3-28 23-45 56-45 0 0 15 14 18 45z" fill="#183f31"/>
                <path d="M67 69c6-9 14-14 25-15" stroke="#d1ad61" stroke-width="5" stroke-linecap="round"/>
                <circle cx="80" cy="56" r="8" fill="#d1ad61"/>
            </g>
        </svg>

        <h1 id="error-title">Algo salió mal</h1>
        <p>El sistema no pudo completar la solicitud en este momento. Intenta nuevamente o vuelve al inicio.</p>
        <a href="{{ $homeUrl }}">Volver al inicio</a>
    </main>
</body>
</html>
