<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CurrentLocationService;
use Illuminate\Support\Facades\Http;

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
