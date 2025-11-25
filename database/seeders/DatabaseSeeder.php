<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrador
        User::create([
            'name' => 'Administrador Demo',
            'email' => 'admin@demo.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // Auditor
        User::create([
            'name' => 'Auditor Demo',
            'email' => 'auditor@demo.com',
            'password' => Hash::make('12345678'),
            'role' => 'auditor',
        ]);

        // Usuario de consulta
        User::create([
            'name' => 'Usuario Consulta',
            'email' => 'consulta@demo.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
        ]);
    }
}
