<?php

namespace Knot\Http\Controllers;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function feed()
    {
        return auth()->user()->feed();
    }
}
