<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

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

        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'cpf' => $faker->cpf(),
            'birth_date' => fake()->date(),
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber()
        ];
    }
}
