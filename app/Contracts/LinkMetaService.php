<?php

declare(strict_types=1);

namespace App\Contracts;

interface LinkMetaService
{
    public function fetch($url);
}
