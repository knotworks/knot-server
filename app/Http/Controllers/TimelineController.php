<?php

namespace Knot\Http\Controllers;

class TimelineController extends Controller
{
    public function show()
    {
        return auth()->user()->timeline();
    }
}
