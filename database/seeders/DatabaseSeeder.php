<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            HabitacionSeeder::class,
        ]);

        $superAdminRole = Role::where('nombre', 'SUPER ADMIN')->first();

        User::updateOrCreate(
            ['email' => 'admin@hot.com'],
            [
                'role_id' => $superAdminRole?->id,
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'estado' => true,
            ]
        );
    }
}