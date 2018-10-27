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

    'driver' => 'gd',
    'upload_quality' => 85,
    'max_size' => 20000,
    'max_width' => 1200,
    'max_height' => 1600,

];
