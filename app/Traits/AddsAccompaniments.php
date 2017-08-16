<?php

namespace Knot\Traits;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait AddsAccompaniments
{
    protected function setAccompaniments(Request $request, $model)
    {
        $this->validate($request, [
            'accompaniments.*.name' => 'required|string',
            'accompaniments.*.user_id' => [
                'nullable',
                'numeric',
                'distinct',
                'exists:users,id',
                Rule::notIn([auth()->id()]),
            ]
        ]);

        $model->addAccompaniments($request->accompaniments);
    }
}
