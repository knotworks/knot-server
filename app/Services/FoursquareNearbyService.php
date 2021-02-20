<?php

namespace Knot\Services;

use Knot\Contracts\NearbyService;
use Illuminate\Support\Facades\Http;

class FoursquareNearbyService implements NearbyService {
    protected $baseUrl = 'https://api.foursquare.com/v2/venues/search';

    public function fetch($lat, $lon, $query = '') {
        $params = [
            'v' => '20200829',
            'll' => "{$lat},{$lon}",
            'client_id' => config('services.foursquare.api_key'),
            'client_secret' => config('services.foursquare.api_secret'),
        ];

        if ($query) {
            $params['query'] = $query;
        }

        $response = Http::get($this->baseUrl, $params);

        return $response->json();

    }
}
