<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OpenCageCurrentLocationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $fakeResponse;

    public function setup(): void
    {
        parent::setup();

        $this->login();
        $this->fakeResponse = ['results' => [['components' => ['city' => 'Toronto']]]];
        Http::fake(['api.opencagedata.com/*' => Http::response($this->fakeResponse, 200)]);
    }

    /** @test */
    public function can_use_the_opencage_service_to_fetch_the_users_current_location_data()
    {
        $this->postJson('api/services/current-location', [
            'lat' => 'fakeLat',
            'lon' => 'fakeLon',
        ])->assertStatus(200)->assertJson($this->fakeResponse);
    }
}
