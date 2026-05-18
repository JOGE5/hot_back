<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\View\View;

class MenuPublicadoController extends Controller
{
    public function __invoke(): View
    {
        $menus = Menu::query()
            ->with([
                'platos' => fn ($query) => $query->orderBy('menu_plato.orden'),
            ])
            ->whereDate('fecha_menu', today())
            ->where('estado', 'Publicado')
            ->orderBy('tipo_menu')
            ->get()
            ->groupBy('tipo_menu');

        $tiposMenu = [
            'Desayuno',
            'Almuerzo',
            'Cena',
            'Especial del día',
        ];

        return view('menu-publicado', [
            'menus' => $menus,
            'tiposMenu' => $tiposMenu,
            'fecha' => today(),
        ]);
    }
}
