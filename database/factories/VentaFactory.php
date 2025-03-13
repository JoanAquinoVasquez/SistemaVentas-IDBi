<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venta>
 */
class VentaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'nombre_cl' => $this->faker->name(),
            'num_iden_cl' => $this->faker->randomElement([
                $this->faker->numerify('########'), // DNI (8 dígitos)
                $this->faker->numerify('###########') // RUC (11 dígitos)
            ]),
            'correo_cl' => $this->faker->optional()->safeEmail(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'monto_total' => $this->faker->randomFloat(2, 50, 5000),
            'fecha_hora' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
