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
                'email' => 'admin@beautyssp.com',
                'password' => Hash::make('Beauty.ssp123**'),
                'permissions' => 'all'
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
