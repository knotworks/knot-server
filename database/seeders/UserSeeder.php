<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
