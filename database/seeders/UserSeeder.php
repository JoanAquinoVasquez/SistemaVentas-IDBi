<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'api'; // Asegurar que se use el guard correcto

        // Buscar el rol en la base de datos con el guard correcto
        $adminRole = Role::where('name', 'admin')->where('guard_name', $guard)->first();
        $vendedorRole = Role::where('name', 'vendedor')->where('guard_name', $guard)->first();

        if (!$adminRole || !$vendedorRole) {
            dd("Error: Los roles no existen en la base de datos con guard_name = api.");
        }

        $admin = User::create([
            'nombre' => 'Joan Edinson',
            'email' => 'jaquinov@unprg.edu.pe',
            'apellido' => 'Aquino Vasquez',
            'password' => Hash::make('jaquinov'),
        ]);

        // Asignar el rol admin asegurando que sea el correcto
        $admin->assignRole($adminRole);

        // Crear 5 vendedores de prueba y asignarles el rol correctamente
        User::factory(5)->create()->each(fn($user) => $user->assignRole($vendedorRole));
    }
}
