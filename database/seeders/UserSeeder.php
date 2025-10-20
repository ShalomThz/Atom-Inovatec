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

        // ============================================================
        // DISEÑADORES
        // ============================================================
        $pedro = User::create([
            'name' => 'Pedro López',
            'email' => 'pedro.lopez@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $pedro->assignRole('disenador');
        $this->command->info('✓ Pedro López (disenador)');

        $sofia = User::create([
            'name' => 'Sofía Torres',
            'email' => 'sofia.torres@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $sofia->assignRole('disenador');
        $this->command->info('✓ Sofía Torres (disenador)');

        // ============================================================
        // TESTER/QA
        // ============================================================
        $miguel = User::create([
            'name' => 'Miguel Ramírez',
            'email' => 'miguel.ramirez@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $miguel->assignRole('tester');
        $this->command->info('✓ Miguel Ramírez (tester)');

        // ============================================================
        // USUARIOS ADICIONALES CON OTROS ROLES
        // ============================================================
        $scrumMaster = User::create([
            'name' => 'Roberto Gómez',
            'email' => 'roberto.gomez@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $scrumMaster->assignRole('scrum_master');
        $this->command->info('✓ Roberto Gómez (scrum_master)');

        $productOwner = User::create([
            'name' => 'Elena Vargas',
            'email' => 'elena.vargas@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $productOwner->assignRole('product_owner');
        $this->command->info('✓ Elena Vargas (product_owner)');

        $stakeholder = User::create([
            'name' => 'Cliente Externo',
            'email' => 'cliente@empresa.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $stakeholder->assignRole('stakeholder');
        $this->command->info('✓ Cliente Externo (stakeholder)');

        $this->command->newLine();
        $this->command->info('✅ 13 usuarios creados y roles asignados');
    }
}
