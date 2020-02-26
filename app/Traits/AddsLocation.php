<?php

namespace Knot\Traits;

use Illuminate\Http\Request;

trait AddsLocation
{
    protected function setLocation(Request $request, $model)
    {
        $this->validate($request, [
            'location.lat' => [
                'required',
                'regex:/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/',
            ],
            'location.long' => [
                'required',
                'regex:/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/',
            ],
        ]);

        $model->addLocation([
            'user_id' => auth()->id(),
            'lat' => $request->input('location.lat'),
            'long' => $request->input('location.long'),
            'name' => $request->input('location.name', null),
            'city' => $request->input('location.city', null),
        ]);
    }
}
