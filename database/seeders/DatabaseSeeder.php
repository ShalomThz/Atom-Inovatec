<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando proceso de seeders...');
        $this->command->newLine();

        $this->call([
            RolePermissionSeeder::class, // Must run first - creates roles and permissions
            UserSeeder::class,           // Then creates users and assigns roles
            ProyectoSeeder::class,
            TareaSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Base de datos poblada exitosamente!');
        $this->command->newLine();

        $this->command->table(
            ['Entidad', 'Cantidad'],
            [
                ['Usuarios', \App\Models\User::count()],
                ['Proyectos', \App\Models\Proyecto::count()],
                ['Tareas', \App\Models\Tarea::count()],
            ]
        );

        $this->command->newLine();
        $this->command->info('📧 Credenciales de acceso:');
        $this->command->info('Email: admin@atominovatec.com');
        $this->command->info('Password: password');
        $this->command->newLine();
    }
}
