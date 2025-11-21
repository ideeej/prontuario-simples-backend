<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create('pt_BR');
        $name = fake()->name();

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'username' => Str::slug($name, '-'),
            'email' => fake()->email(),
            'document' => $faker->cpf(),
            'birth_date' => fake()->date(),
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
        ];
    }
}
