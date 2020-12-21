<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PartidasSeeder::class);
        // $this->call(ProductoTableSeeder::class);
        // $this->call(UsersTableSeeder::class);
    }
}
