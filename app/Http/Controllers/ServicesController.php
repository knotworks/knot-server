<?php

namespace Knot\Http\Controllers;

use Cloudinary\Api\ApiUtils;
use Illuminate\Http\Request;
use Knot\Contracts\CurrentLocationService;
use Knot\Contracts\LinkMetaService;
use Knot\Contracts\NearbyService;

class ServicesController extends Controller
{
    public function fetchNearby(Request $request, NearbyService $nearby)
    {
        $request->validate([
            'lat' => 'required',
            'lon' => 'required',
        ]);

        return $nearby->fetch(...array_values($request->only(['lat', 'lon', 'query'])));
    }

    public function fetchCurrentLocation(Request $request, CurrentLocationService $currentLocation)
    {
        $request->validate([
            'lat' => 'required',
            'lon' => 'required',
        ]);

        return $currentLocation->fetch(...array_values($request->only(['lat', 'lon'])));
    }

    public function fetchLinkMeta(Request $request, LinkMetaService $linkMeta)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        return $linkMeta->fetch($request->input('url'));
    }

    public function generateCloudinarySignature(Request $request)
    {
        $request->validate([
            'timestamp' => 'required',
        ]);

        return ApiUtils::signParameters(config('services.cloudinary.secret'), $request->only('timestamp', 'upload_preset', 'folder'));
    }
}
