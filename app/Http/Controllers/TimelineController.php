<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;

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
