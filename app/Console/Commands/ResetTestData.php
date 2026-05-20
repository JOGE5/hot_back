<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetTestData extends Command
{
    protected $signature = 'hotel:reset-test-data';

    protected $description = 'Limpia datos operativos de prueba y conserva el SUPER ADMIN base.';

    private const SUPER_ADMIN_EMAIL = 'superadmin@lamansion.test';

    private const SUPER_ADMIN_PASSWORD = '12345678';

    /**
     * @var array<int, string>
     */
    private array $tablesToClean = [
        'logs_user',
        'personal_access_tokens',
        'pagos',
        'reservaciones',
        'huespedes',
        'menu_plato',
        'ingrediente_plato',
        'menus',
        'platos',
        'ingredientes',
        'paquetes',
        'tours',
        'habitaciones',
        'users',
    ];

    public function handle(): int
    {
        $connection = DB::connection();
        $connectionName = $connection->getName();
        $databaseName = $connection->getDatabaseName();

        $this->warn('Limpieza controlada de datos de prueba del sistema HOT');
        $this->line("Conexion activa: {$connectionName}");
        $this->line("Base de datos activa: {$databaseName}");
        $this->newLine();

        $this->info('Conteos antes de limpiar:');
        $beforeCounts = $this->getCounts();
        $this->renderCounts($beforeCounts);
        $this->newLine();

        $this->warn('ADVERTENCIA: esta accion borrara datos operativos de prueba.');
        $this->warn('No se borraran roles, migraciones, estructura ni tablas de sistema Laravel.');
        $this->warn('No se reiniciaran AUTO_INCREMENT ni se desactivaran FOREIGN_KEY_CHECKS.');
        $this->newLine();

        if (! $this->confirm('Deseas continuar con la limpieza?', false)) {
            $this->info('Operacion cancelada. No se realizaron cambios.');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function (): void {
                foreach ($this->tablesToClean as $table) {
                    if ($table === 'users') {
                        DB::table('users')
                            ->where('email', '!=', self::SUPER_ADMIN_EMAIL)
                            ->delete();

                        continue;
                    }

                    DB::table($table)->delete();
                }

                $this->ensureSuperAdmin();
            });
        } catch (\Throwable $exception) {
            $this->error('La limpieza fallo y la transaccion fue revertida.');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Conteos despues de limpiar:');
        $afterCounts = $this->getCounts();
        $this->renderCounts($afterCounts);
        $this->newLine();

        $this->info('SUPER ADMIN base disponible:');
        $this->line('Email: '.self::SUPER_ADMIN_EMAIL);
        $this->line('Password: '.self::SUPER_ADMIN_PASSWORD);
        $this->line('Rol: SUPER ADMIN');
        $this->line('Estado: activo');

        return self::SUCCESS;
    }

    /**
     * @return array<string, int>
     */
    private function getCounts(): array
    {
        $counts = [];

        foreach ($this->tablesToClean as $table) {
            $counts[$table] = DB::table($table)->count();
        }

        $counts['roles'] = DB::table('roles')->count();

        return $counts;
    }

    /**
     * @param array<string, int> $counts
     */
    private function renderCounts(array $counts): void
    {
        $rows = [];

        foreach ($counts as $table => $count) {
            $rows[] = [$table, $count];
        }

        $this->table(['Tabla', 'Registros'], $rows);
    }

    private function ensureSuperAdmin(): void
    {
        $now = now();

        $role = DB::table('roles')
            ->where('nombre', 'SUPER ADMIN')
            ->first();

        if (! $role) {
            $roleId = DB::table('roles')->insertGetId([
                'nombre' => 'SUPER ADMIN',
                'descripcion' => 'Acceso total al sistema.',
                'estado' => true,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        } else {
            $roleId = $role->id;

            DB::table('roles')
                ->where('id', $roleId)
                ->update([
                    'estado' => true,
                    'deleted_at' => null,
                    'updated_at' => $now,
                ]);
        }

        $userData = [
            'role_id' => $roleId,
            'nombres' => 'Super',
            'apellido_paterno' => 'Admin',
            'apellido_materno' => null,
            'name' => 'Super Admin',
            'password' => bcrypt(self::SUPER_ADMIN_PASSWORD),
            'estado' => true,
            'email_verified_at' => $now,
            'deleted_at' => null,
            'updated_at' => $now,
        ];

        $superAdmin = DB::table('users')
            ->where('email', self::SUPER_ADMIN_EMAIL)
            ->first();

        if ($superAdmin) {
            DB::table('users')
                ->where('id', $superAdmin->id)
                ->update($userData);

            return;
        }

        DB::table('users')->insert(array_merge($userData, [
            'email' => self::SUPER_ADMIN_EMAIL,
            'created_at' => $now,
        ]));
    }
}
