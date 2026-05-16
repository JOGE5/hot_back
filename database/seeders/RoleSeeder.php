<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'SUPER ADMIN',
                'descripcion' => 'Acceso total al sistema y gestión completa de usuarios administrativos.',
            ],
            [
                'nombre' => 'ADMIN',
                'descripcion' => 'Acceso administrativo general, gestión de huéspedes, recepcionistas, chefs y reportes.',
            ],
            [
                'nombre' => 'RECEPCIONISTA',
                'descripcion' => 'Acceso a huéspedes, reservaciones y habitaciones para controlar el flujo hotelero.',
            ],
            [
                'nombre' => 'CHEF',
                'descripcion' => 'Acceso al inventario de materia prima y menú diario.',
            ],
            [
                'nombre' => 'HUESPED',
                'descripcion' => 'Acceso al panel de huésped, reservas y perfil propio.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['nombre' => $role['nombre']],
                [
                    'descripcion' => $role['descripcion'],
                    'estado' => true,
                ]
            );
        }
    }
}