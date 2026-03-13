<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\CurrentLocationService;
use App\Contracts\LinkMetaService;
use App\Contracts\NearbyService;
use Illuminate\Http\Request;

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
}
