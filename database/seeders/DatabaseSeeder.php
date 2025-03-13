<?php

namespace Database\Seeders;

use App\Models\DetalleVenta;
use App\Models\Product;
use App\Models\User;
use App\Models\Venta;
use Database\Seeders\UserSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        DB::transaction(function () {
            $this->call([RolePermissionSeeder::class, UserSeeder::class]);
            Product::factory()->count(50)->create();
            Venta::factory()->count(50)->create();
            DetalleVenta::factory()->count(100)->create();
        });
    }
}
