<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menú del día | Hotel Club Campestre La Mansión</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f7efe3;
            --paper: #fffaf2;
            --paper-strong: #ffffff;
            --ink: #2f261c;
            --muted: #756655;
            --accent: #9b6a2f;
            --accent-soft: #efe0c9;
            --line: rgba(96, 66, 34, 0.16);
            --shadow: 0 18px 45px rgba(75, 49, 22, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.82), transparent 32rem),
                linear-gradient(180deg, #fbf5ec 0%, var(--bg) 100%);
            color: var(--ink);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 34px 0 52px;
        }

        .hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            padding: 28px 0 24px;
            border-bottom: 1px solid var(--line);
        }

        .eyebrow {
            color: var(--accent);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        h1 {
            margin: 8px 0 0;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(34px, 6vw, 64px);
            line-height: 1;
            letter-spacing: 0;
        }

        .date-pill {
            flex: none;
            padding: 10px 14px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.62);
            color: var(--muted);
            font-size: 14px;
            font-weight: 800;
        }

        .empty {
            margin-top: 42px;
            padding: 34px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--paper);
            box-shadow: var(--shadow);
            text-align: center;
        }

        .empty h2 {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 30px;
            letter-spacing: 0;
        }

        .empty p {
            margin: 10px 0 0;
            color: var(--muted);
        }

        .menu-section {
            padding-top: 34px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .section-heading h2 {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 32px;
            letter-spacing: 0;
        }

        .count {
            color: var(--accent);
            font-size: 13px;
            font-weight: 800;
            background: var(--accent-soft);
            border-radius: 999px;
            padding: 7px 11px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .dish-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--paper-strong);
            box-shadow: var(--shadow);
        }

        .dish-image {
            width: 100%;
            aspect-ratio: 4 / 3;
            background: #eadcc8;
            overflow: hidden;
        }

        .dish-image img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .dish-placeholder {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: #9b7c56;
            font-weight: 800;
            background: linear-gradient(135deg, #f3e4ce 0%, #ead5b9 100%);
        }

        .dish-body {
            padding: 16px;
        }

        .dish-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .dish-name {
            margin: 0;
            color: var(--ink);
            font-size: 19px;
            line-height: 1.2;
            font-weight: 850;
            letter-spacing: 0;
        }

        .price {
            flex: none;
            color: #ffffff;
            background: var(--accent);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: 850;
        }

        .category {
            display: inline-flex;
            margin-top: 12px;
            color: var(--accent);
            background: var(--accent-soft);
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 800;
        }

        .description {
            margin: 12px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media (max-width: 900px) {
            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .page {
                width: min(100% - 24px, 1180px);
                padding-top: 18px;
            }

            .hero {
                display: block;
            }

            .date-pill {
                display: inline-flex;
                margin-top: 16px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .section-heading {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="hero">
            <div>
                <div class="eyebrow">Hotel Club Campestre La Mansión</div>
                <h1>Menú del día</h1>
            </div>

            <div class="date-pill">
                {{ $fecha->format('d/m/Y') }}
            </div>
        </header>

        @if($menus->isEmpty())
            <section class="empty">
                <h2>Aún no hay menú publicado para hoy.</h2>
                <p>Nuestro equipo de cocina publicará las opciones del día en cuanto estén listas.</p>
            </section>
        @else
            @foreach($tiposMenu as $tipoMenu)
                @php
                    $menusDelTipo = $menus->get($tipoMenu, collect());
                    $platosDelTipo = $menusDelTipo
                        ->flatMap(fn ($menu) => $menu->platos)
                        ->sortBy('pivot.orden');
                @endphp

                @if($platosDelTipo->isNotEmpty())
                    <section class="menu-section">
                        <div class="section-heading">
                            <h2>{{ $tipoMenu }}</h2>
                            <div class="count">{{ $platosDelTipo->count() }} platos</div>
                        </div>

                        <div class="grid">
                            @foreach($platosDelTipo as $plato)
                                @php
                                    $imagenUrl = $plato->imagen ? asset('storage/' . $plato->imagen) : null;
                                @endphp

                                <article class="dish-card">
                                    <div class="dish-image">
                                        @if($imagenUrl)
                                            <img src="{{ $imagenUrl }}" alt="{{ $plato->nombre }}">
                                        @else
                                            <div class="dish-placeholder">Sin imagen</div>
                                        @endif
                                    </div>

                                    <div class="dish-body">
                                        <div class="dish-top">
                                            <h3 class="dish-name">{{ $plato->nombre }}</h3>
                                            <div class="price">Bs. {{ number_format((float) $plato->precio, 2) }}</div>
                                        </div>

                                        <div class="category">{{ $plato->categoria }}</div>

                                        <p class="description">
                                            {{ $plato->descripcion ?: 'Sin descripción disponible.' }}
                                        </p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        @endif
    </main>
</body>
</html>
