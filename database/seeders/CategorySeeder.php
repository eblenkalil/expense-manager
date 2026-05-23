<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Alimentação',
            'Transporte',
            'Hospedagem',
            'Combustível',
            'Estacionamento',
            'Material de Escritório',
            'Outros',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
