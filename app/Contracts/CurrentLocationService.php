<?php

namespace Knot\Contracts;

interface CurrentLocationService
{
    public function fetch($lat, $lon);
}
