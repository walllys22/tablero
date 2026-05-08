<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class PersonaFactory extends Factory
{
    protected $model = Persona::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ci' => fake()->numerify('########'),
            'first_name' => fake()->name(),
            'birth_date' => fake()->dateTimeBetween('-45 years', '-8 years')->format('Y-m-d'),
            'email' => fake()->optional()->safeEmail(),
            'country_code' => '591',
            'phone' => fake()->optional()->numerify('########'),
            'address' => fake()->optional()->address(),
            'gender' => fake()->randomElement(['Masculino', 'Femenino']),
            'sangre' => fake()->optional()->randomElement(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-']),
            'image' => null,
            'status' => 1,
        ];
    }
}
