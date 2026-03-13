<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class TimelineController extends Controller
{
    public function show()
    {
        return auth()->user()->timeline();
    }
}
