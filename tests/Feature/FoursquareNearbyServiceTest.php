<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FoursquareNearbyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $fakeResponse;

    public function setup(): void
    {
        parent::setup();

        $this->login();
        $this->fakeResponse = ['venues' => ['Venue A', 'Venue B', 'Venue C']];
        Http::fake(['api.foursquare.com/*' => Http::response($this->fakeResponse, 200)]);
    }

    /** @test */
    public function can_use_the_foursquare_service_to_fetch_nearby_locations()
    {
        $this->postJson('api/services/nearby', [
            'lat' => 'fakeLat',
            'lon' => 'fakeLon',
            'query' => 'Fake Query',
        ])->assertStatus(200)->assertJson($this->fakeResponse);
    }
}
