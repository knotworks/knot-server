<?php

namespace Knot\Traits;

use Illuminate\Http\Request;

trait AddsAccompaniments
{
    protected function setAccompaniments(Request $request, $model)
    {
        $this->validate($request, [
              'accompaniments.*.name' => 'required',
          ]);

        $model->addAccompaniments($request->accompaniments);
    }
}
