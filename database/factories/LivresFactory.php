<?php

namespace Database\Factories;

use App\Models\Categories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livres>
 */
class LivresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(),
             'description' => fake()->paragraph(),
              'date_sortie' => fake()->date(),
              'slug'  => (string) Str::uuid(),
               'categorie_id' =>  Categories::factory(),
                'user_id' => User::factory(),
        ];
    }
}
