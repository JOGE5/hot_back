<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Habitacion;

class HabitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $habitaciones = [
            [
                'numero' => '101',
                'tipo' => 'Simple',
                'capacidad' => 1,
                'precio_noche' => 120.00,
                'estado' => 'Disponible',
                'descripcion' => 'Habitación simple con cama individual, ideal para viajeros solitarios.',
                'activo' => true,
            ],
            [
                'numero' => '102',
                'tipo' => 'Doble',
                'capacidad' => 2,
                'precio_noche' => 180.00,
                'estado' => 'Disponible',
                'descripcion' => 'Habitación doble con dos camas individuales o una matrimonial.',
                'activo' => true,
            ],
            [
                'numero' => '103',
                'tipo' => 'Matrimonial',
                'capacidad' => 2,
                'precio_noche' => 220.00,
                'estado' => 'Disponible',
                'descripcion' => 'Habitación con cama matrimonial king size, confortable y espaciosa.',
                'activo' => true,
            ],
            [
                'numero' => '104',
                'tipo' => 'Familiar',
                'capacidad' => 4,
                'precio_noche' => 320.00,
                'estado' => 'Disponible',
                'descripcion' => 'Habitación familiar para 4 personas con excelente vista.',
                'activo' => true,
            ],
            [
                'numero' => '105',
                'tipo' => 'Suite',
                'capacidad' => 2,
                'precio_noche' => 450.00,
                'estado' => 'Disponible',
                'descripcion' => 'Suite de lujo con jacuzzi y servicios premium.',
                'activo' => true,
            ],
        ];

        foreach ($habitaciones as $habitacion) {
            Habitacion::updateOrCreate(
                ['numero' => $habitacion['numero']],
                $habitacion
            );
        }
    }
}
