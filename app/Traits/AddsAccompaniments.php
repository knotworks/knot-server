<?php

namespace Knot\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait AddsAccompaniments
{
    protected function setAccompaniments(Request $request, $model)
    {
        $validator = Validator::make($request->all(), [
            'accompaniments' => 'present|array|min:0',
            'accompaniments.*.id' => [
                'nullable',
                'numeric',
                'distinct',
                'exists:users,id',
                Rule::notIn([auth()->id()]),
            ],
        ]);

        if ($validator->fails()) {
            $model->delete();

            return response($validator->errors(), 422);
        }

        $model->addAccompaniments(collect($request->accompaniments)->map(function ($accompaniment) {
            return ['user_id' => (int) $accompaniment['id']];
        }));
    }
}
