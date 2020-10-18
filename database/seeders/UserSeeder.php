<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Knot\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'first_name' => 'Collin',
            'last_name' => 'Henderson',
            'email' => 'collin@syropia.net',
            'password' => 'password',
        ]);
    }
}
