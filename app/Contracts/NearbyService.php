<?php

namespace Knot\Contracts;

interface NearbyService
{
    public function fetch($lat, $lon, $query = '');
}
