<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================================
        // USUARIO ADMINISTRADOR (Super Admin)
        // ============================================================
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('super_admin');
        $this->command->info('✓ Administrador (super_admin)');

        // ============================================================
        // LÍDERES DE PROYECTO
        // ============================================================
        $juan = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $juan->assignRole('lider_proyecto');
        $this->command->info('✓ Juan Pérez (lider_proyecto)');

        $maria = User::create([
            'name' => 'María García',
            'email' => 'maria.garcia@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $maria->assignRole('lider_proyecto');
        $this->command->info('✓ María García (lider_proyecto)');

        // ============================================================
        // DESARROLLADORES
        // ============================================================
        $carlos = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos.rodriguez@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $carlos->assignRole('desarrollador');
        $this->command->info('✓ Carlos Rodríguez (desarrollador)');

        $ana = User::create([
            'name' => 'Ana Martínez',
            'email' => 'ana.martinez@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $ana->assignRole('desarrollador');
        $this->command->info('✓ Ana Martínez (desarrollador)');

        $luis = User::create([
            'name' => 'Luis Fernández',
            'email' => 'luis.fernandez@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $luis->assignRole('desarrollador');
        $this->command->info('✓ Luis Fernández (desarrollador)');

        $laura = User::create([
            'name' => 'Laura Sánchez',
            'email' => 'laura.sanchez@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $laura->assignRole('desarrollador');
        $this->command->info('✓ Laura Sánchez (desarrollador)');

        $this->command->newLine();
        $this->command->info('✅ 7 usuarios creados y roles asignados');
    }
}
