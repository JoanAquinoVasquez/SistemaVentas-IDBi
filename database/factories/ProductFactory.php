<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->regexify('[A-Z0-9]{8}'),
            'nombre' => $this->faker->words(3, true),
            'precio_unitario' => $this->faker->randomFloat(2, 1, 1000),
            'stock' => $this->faker->numberBetween(0, 500),
        ];
    }
}
