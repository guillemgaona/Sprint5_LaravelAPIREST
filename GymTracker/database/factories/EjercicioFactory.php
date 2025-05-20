<?php

namespace Database\Factories;

use App\Models\Ejercicio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ejercicio>
 */
class EjercicioFactory extends Factory
{

    protected $model = Ejercicio::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->words(3, true), 
            'grupo_muscular' => $this->faker->randomElement(['pecho', 'espalda', 'piernas', 'hombros', 'brazos', 'core', 'otros']),
            'descripcion' => $this->faker->optional()->sentence, 
            'imagen_demo' => $this->faker->optional()->imageUrl(640, 480, 'sports'), 
        ];
    }
}