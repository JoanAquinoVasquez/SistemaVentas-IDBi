<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Venta;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetalleVenta>
 */
class DetalleVentaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'venta_id' => Venta::inRandomOrder()->first()->id ?? Venta::factory()->create()->id,
            'product_id' => Product::inRandomOrder()->first()->id ?? Product::factory()->create()->id,
            'cantidad' => $this->faker->numberBetween(1, 10),
        ];
    }
}
