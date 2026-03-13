<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->login();
    $this->fakeResponse = ['venues' => ['Venue A', 'Venue B', 'Venue C']];

    Http::fake(['api.foursquare.com/*' => Http::response($this->fakeResponse, 200)]);
});

it('can use the foursquare service to fetch nearby locations', function (): void {
    $this->postJson('api/services/nearby', [
        'lat' => 'fakeLat',
        'lon' => 'fakeLon',
        'query' => 'Fake Query',
    ])->assertStatus(200)->assertJson($this->fakeResponse);
});
