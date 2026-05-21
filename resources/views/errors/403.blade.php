@php
    $homeUrl = auth()->check() ? url('/admin') : url('/');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lo sentimos | Hotel La Mansión</title>
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

        .key {
            transform-origin: 108px 52px;
            animation: keyTurn 3.5s ease-in-out infinite;
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

        @keyframes keyTurn {
            0%, 100% { transform: rotate(0deg); }
            45%, 60% { transform: rotate(-8deg); }
        }
    </style>
</head>
<body>
    <main class="card" aria-labelledby="error-title">
        <div class="brand">Hotel Club Campestre La Mansión</div>

        <svg class="illustration" viewBox="0 0 160 160" role="img" aria-label="Puerta cerrada con llave">
            <rect x="44" y="28" width="72" height="106" rx="8" fill="#6b4428"/>
            <rect x="53" y="38" width="54" height="88" rx="5" fill="#8a5a34"/>
            <path d="M58 44h44v76H58z" fill="#6f472b"/>
            <circle cx="94" cy="82" r="4" fill="#d1ad61"/>
            <path d="M43 134h74" stroke="#c7a353" stroke-width="6" stroke-linecap="round"/>
            <g class="key">
                <circle cx="47" cy="58" r="14" fill="none" stroke="#d1ad61" stroke-width="8"/>
                <path d="M60 58h44m-12 0v12m-13-12v8" stroke="#d1ad61" stroke-width="8" stroke-linecap="round"/>
            </g>
        </svg>

        <h1 id="error-title">Lo sentimos</h1>
        <p>No pudimos permitir el acceso a esta sección. Es posible que tu usuario no tenga permisos para continuar.</p>
        <a href="{{ $homeUrl }}">Volver al inicio</a>
    </main>
</body>
</html>
