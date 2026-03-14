<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\CurrentLocationService;
use App\Contracts\LinkMetaService;
use App\Contracts\NearbyService;
use Illuminate\Http\Request;

/**
 * @group Services
 *
 * Utility endpoints for nearby places, reverse geocoding, and link previews.
 *
 * @authenticated
 */
class ServicesController extends Controller
{
    /**
     * Fetch nearby places.
     *
     * @bodyParam lat number required Latitude. Example: 43.6532
     * @bodyParam lon number required Longitude. Example: -79.3832
     * @bodyParam query string Optional search text to narrow the nearby results. Example: coffee
     *
     * @response 200 [{"name":"Coffee Shop","lat":43.6532,"lon":-79.3832}]
     */
    public function fetchNearby(Request $request, NearbyService $nearby)
    {
        $request->validate([
            'lat' => 'required',
            'lon' => 'required',
        ]);

        return $nearby->fetch(...array_values($request->only(['lat', 'lon', 'query'])));
    }

    /**
     * Reverse geocode a coordinate.
     *
     * @bodyParam lat number required Latitude. Example: 43.6532
     * @bodyParam lon number required Longitude. Example: -79.3832
     *
     * @response 200 {"formatted":"Toronto, ON, Canada"}
     */
    public function fetchCurrentLocation(Request $request, CurrentLocationService $currentLocation)
    {
        $request->validate([
            'lat' => 'required',
            'lon' => 'required',
        ]);

        return $currentLocation->fetch(...array_values($request->only(['lat', 'lon'])));
    }

    /**
     * Fetch link metadata.
     *
     * @bodyParam url string required A fully-qualified URL. Example: https://example.com/story
     *
     * @response 200 {"title":"Example Story","description":"A useful preview.","image":"https://example.com/preview.jpg"}
     */
    public function fetchLinkMeta(Request $request, LinkMetaService $linkMeta)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        return $linkMeta->fetch($request->input('url'));
    }
}
