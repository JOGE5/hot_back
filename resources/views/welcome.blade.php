<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Hotel Club Campestre La Mansión</title>

    <style>
        :root {
            --verde-noche: #071b13;
            --verde-profundo: #123525;
            --verde: #1d4a34;
            --beige: #f4ead8;
            --marfil: #fffaf0;
            --dorado: #c8a45d;
            --texto: #24362c;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Georgia", "Times New Roman", serif;
            color: var(--marfil);
            background:
                linear-gradient(120deg, rgba(7, 27, 19, 0.96), rgba(18, 53, 37, 0.9)),
                var(--verde-noche);
            display: grid;
            place-items: center;
            padding: 28px;
        }

        .page {
            width: min(100%, 1060px);
            min-height: 640px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(234, 208, 138, 0.42);
            border-radius: 26px;
            background:
                linear-gradient(115deg, rgba(7, 27, 19, 0.96) 0%, rgba(18, 53, 37, 0.95) 54%, rgba(244, 234, 216, 0.94) 54.2%),
                var(--verde-profundo);
            box-shadow: 0 34px 90px rgba(0, 0, 0, 0.42);
        }

        .page::before {
            content: "";
            position: absolute;
            inset: 22px;
            border: 1px solid rgba(234, 208, 138, 0.34);
            border-radius: 20px;
            pointer-events: none;
        }

        .page::after {
            content: "";
            position: absolute;
            width: 360px;
            height: 360px;
            right: -130px;
            top: -120px;
            border: 1px solid rgba(200, 164, 93, 0.28);
            border-radius: 50%;
            pointer-events: none;
        }

        .layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            min-height: 640px;
        }

        .hero {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(42px, 7vw, 82px);
        }

        .brand-mark {
            width: min(260px, 72vw);
            margin-bottom: 42px;
            padding: 18px 20px;
            border: 1px solid rgba(234, 208, 138, 0.45);
            border-radius: 18px;
            background: rgba(255, 250, 240, 0.06);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .brand-mark img {
            width: 100%;
            height: auto;
            display: block;
        }

        h1 {
            max-width: 620px;
            margin: 0;
            color: var(--marfil);
            font-size: clamp(38px, 6vw, 68px);
            line-height: 0.98;
            font-weight: 700;
        }

        .panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(34px, 6vw, 72px);
            color: var(--texto);
        }

        .access {
            width: min(100%, 360px);
            text-align: center;
        }

        .access__line {
            width: 88px;
            height: 3px;
            margin: 0 auto 28px;
            background: linear-gradient(90deg, transparent, var(--dorado), transparent);
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 56px;
            margin-top: 30px;
            padding: 0 26px;
            border: 1px solid rgba(7, 27, 19, 0.08);
            border-radius: 999px;
            background: linear-gradient(135deg, var(--verde), var(--verde-noche));
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 18px 32px rgba(7, 27, 19, 0.24);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .button:hover,
        .button:focus {
            transform: translateY(-2px);
            box-shadow: 0 22px 38px rgba(7, 27, 19, 0.3);
        }

        .button:focus-visible {
            outline: 3px solid var(--dorado);
            outline-offset: 4px;
        }

        @media (max-width: 820px) {
            body {
                padding: 18px;
            }

            .page,
            .layout {
                min-height: auto;
            }

            .page {
                background: linear-gradient(180deg, rgba(7, 27, 19, 0.97) 0%, rgba(18, 53, 37, 0.96) 58%, var(--beige) 58.2%);
            }

            .layout {
                grid-template-columns: 1fr;
            }

            .hero,
            .panel {
                padding: 42px 30px;
            }

            .brand-mark {
                margin-bottom: 32px;
            }
        }

        @media (max-width: 480px) {
            .page::before {
                inset: 12px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="layout">
            <section class="hero" aria-labelledby="titulo-principal">
                <div class="brand-mark">
                    <img src="{{ asset('images/logo-la-mansion.png') }}" alt="La Mansión Hotel Club Campestre">
                </div>

                <h1 id="titulo-principal">Hotel Club Campestre La Mansión</h1>
            </section>

            <aside class="panel" aria-label="Acceso">
                <div class="access">
                    <div class="access__line"></div>

                    <a class="button" href="{{ url('/admin') }}">
                        Acceder
                    </a>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
