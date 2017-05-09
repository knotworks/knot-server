<?php

namespace FamJam\Http\Controllers;

use Illuminate\Http\Request;

class AccompanimentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store($postId)
    {
        
    }
}
