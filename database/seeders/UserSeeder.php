<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@atominovatec.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol de super_admin
       // $superAdmin->assignRole('super_admin');
    }
}
