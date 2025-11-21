<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // 1. Cria os 2 usuários terapeutas
        $users = User::factory(2)->create();

        // 2. Para cada usuário, cria 2 pacientes associados a ele.
        $users->each(function ($user) {
            Patient::factory(2)->create(['user_id' => $user->id]);
        });
    }
}
