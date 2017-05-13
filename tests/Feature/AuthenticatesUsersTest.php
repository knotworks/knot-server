<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Laravel\Passport\Passport;
use FamJam\Models\User;

class FetchesUserTest extends TestCase
{
    use DatabaseMigrations;

    
    /** @test */
    function can_register_a_new_user()
    {
        $userData =  [
            'name' => 'Jane Doe',
            'email' => 'jane@janedoe.com',
            'password' => 'foobar'
        ];
        $response = $this->json('POST', 'api/auth/user', $userData);

        $response->assertStatus(200);

        $this->assertEquals(1, User::count());
    }
    
    /** @test */
    function can_fetch_an_authenticated_user()
    {
        $this->authenticate();
        
        $response = $this->get('api/auth/user');

        $response->assertStatus(200);
    }
    
}