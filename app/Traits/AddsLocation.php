<?php

namespace Knot\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait AddsLocation
{
    protected function setLocation(Request $request, $model)
    {
        $validator = Validator::make($request->all(), [
            'location.lat' => [
                'required',
                'regex:/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/',
            ],
            'location.long' => [
                'required',
                'regex:/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/',
            ],
        ]);

        if ($validator->fails()) {
            $model->delete();

            return response($validator->errors(), 422);
        }

        $model->addLocation([
            'user_id' => auth()->id(),
            'lat' => $request->input('location.lat'),
            'long' => $request->input('location.long'),
            'name' => $request->input('location.name', null),
            'city' => $request->input('location.city', null),
        ]);
    }
}
