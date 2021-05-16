<?php

namespace Knot\Services;

use Illuminate\Support\Facades\Http;
use Knot\Contracts\CurrentLocationService;

class OpenCageCurrentLocationService implements CurrentLocationService
{
    protected $baseUrl = 'https://api.opencagedata.com/geocode/v1/json';

    public function fetch($lat, $lon)
    {
        $params = [
            'q' => "{$lat}+{$lon}",
            'key' => config('services.opencage.api_key'),
        ];

        $response = Http::get($this->baseUrl, $params);

        return $response->json();
    }
}
