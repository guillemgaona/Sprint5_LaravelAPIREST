<?php

namespace Database\Factories;

use App\Models\Sesion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sesion>
 */
class SesionFactory extends Factory
{
   
    protected $model = Sesion::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'fecha' => $this->faker->date(),
            'nota' => $this->faker->optional()->paragraph,
        ];
    }
}