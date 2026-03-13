<?php

declare(strict_types=1);

namespace App\Contracts;

interface CurrentLocationService
{
    public function fetch($lat, $lon);
}
