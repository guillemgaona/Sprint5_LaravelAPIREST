<?php

namespace Database\Factories;

use App\Models\Serie; 
use App\Models\Sesion; 
use App\Models\Ejercicio; 
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Serie>
 */
class SerieFactory extends Factory
{
    protected $model = Serie::class;

    public function definition(): array
    {
        return [
            'id_sesion' => Sesion::factory(),
            'id_ejercicio' => Ejercicio::factory(),
            'serie_num' => $this->faker->numberBetween(1, 5),
            'repeticiones' => $this->faker->numberBetween(5, 15),
            'peso' => $this->faker->randomFloat(2, 0, 200), 
        ];
    }
}