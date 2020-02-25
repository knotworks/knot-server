<?php

namespace Knot\Http\Controllers;

class TimelineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show()
    {
        return auth()->user()->timeline();
    }
}
