<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Warehouses;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $userAdmin = User::where(['email' => 'admin@beautyssp.com'])->first();
        if(!isset($userAdmin)){
            User::create([
                'name' => 'Admin',
                'lastname' => 'BeautySSP',
                'type' => 'user',
                'email' => 'admin@beautyssp.com',
                'password' => Hash::make('Beauty.ssp123**'),
                'permissions' => '[{"item":"DATOS DE REGISTRO","level":0,"expandable":true},{"item":"Bodegas","level":1,"expandable":true},{"item":"Bodegas registradas","level":2,"expandable":false},{"item":"Crear bodega","level":2,"expandable":false},{"item":"Inventario","level":2,"expandable":false},{"item":"Proveedores","level":1,"expandable":true},{"item":"Lista de proveedores","level":2,"expandable":false},{"item":"Agregar proveedor","level":2,"expandable":false},{"item":"Productos","level":1,"expandable":true},{"item":"Lista de productos","level":2,"expandable":false},{"item":"Agregar productos","level":2,"expandable":false},{"item":"Agregar categorías","level":2,"expandable":false},{"item":"Agregar subcategorías","level":2,"expandable":false},{"item":"Clientes","level":1,"expandable":true},{"item":"Lista de clientes","level":2,"expandable":false},{"item":"Agregar cliente","level":2,"expandable":false},{"item":"Facturación","level":1,"expandable":true},{"item":"Lista de facturas","level":2,"expandable":false},{"item":"Nueva factura","level":2,"expandable":false},{"item":"USUARIO","level":0,"expandable":true},{"item":"Usuarios","level":1,"expandable":true},{"item":"Lista de usuarios","level":2,"expandable":false},{"item":"Agregar usuario","level":2,"expandable":false}]'
            ]);
        }

        $warehouse = Warehouses::where(['description' => 'Bodega principal'])->first();
        if(!isset($warehouse)){
            Warehouses::create([
                'description' => 'Bodega principal',
                'create_by' => 1
            ]);
        }

    }
}
