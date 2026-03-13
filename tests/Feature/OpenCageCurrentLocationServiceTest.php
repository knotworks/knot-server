<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->login();
    $this->fakeResponse = ['results' => [['components' => ['city' => 'Toronto']]]];

    Http::fake(['api.opencagedata.com/*' => Http::response($this->fakeResponse, 200)]);
});

it('can use the opencage service to fetch the users current location data', function (): void {
    $this->postJson('api/services/current-location', [
        'lat' => 'fakeLat',
        'lon' => 'fakeLon',
    ])->assertStatus(200)->assertJson($this->fakeResponse);
});
