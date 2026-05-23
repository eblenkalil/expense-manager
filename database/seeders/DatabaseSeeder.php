<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Cria o primeiro usuário admin
        User::firstOrCreate(
            ['email' => 'admin@suaempresa.com'],
            [
                'name'         => 'Administrador',
                'password'     => Hash::make('Admin@1234'),
                'role'         => 'admin',
                'notify_email' => true,
            ]
        );

        $this->call(CategorySeeder::class);
    }
}
