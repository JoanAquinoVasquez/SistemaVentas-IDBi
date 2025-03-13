<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Definir el guard correcto
        $guard = 'api';

        // Crear permisos con guard API
        $createUpdateProduct = Permission::create(['name' => 'Registrar y actualizar productos', 'guard_name' => $guard]);
        $listProducts = Permission::create(['name' => 'Listar productos', 'guard_name' => $guard]);
        $createSale = Permission::create(['name' => 'Registrar ventas', 'guard_name' => $guard]);

        // Crear roles con guard API
        $admin = Role::create(['name' => 'admin', 'guard_name' => $guard]);
        $vendedor = Role::create(['name' => 'vendedor', 'guard_name' => $guard]);

        // Asignar permisos al rol admin
        $admin->givePermissionTo([$createUpdateProduct, $listProducts, $createSale]);

        // Asignar permisos al rol vendedor
        $vendedor->givePermissionTo([$createSale, $listProducts]);
    }
}
