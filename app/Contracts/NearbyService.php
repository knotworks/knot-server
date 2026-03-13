<?php

declare(strict_types=1);

namespace App\Contracts;

interface NearbyService
{
    public function fetch($lat, $lon, $query = '');
}
