<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
 */

    'driver' => 'imagick',
    'upload_quality' => 100,
    'max_width' => 2400,
    'max_height' => 3200,
    'max_size' => 40000,

];
