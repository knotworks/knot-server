<?php

declare(strict_types=1);

return [
    'ffmpeg' => [
        'binaries' => env('FFMPEG_BINARIES', 'ffmpeg'),
        'threads' => 12,
    ],

    'ffprobe' => [
        'binaries' => env('FFPROBE_BINARIES', 'ffprobe'),
    ],

    'timeout' => (int) env('FFMPEG_TIMEOUT', 3600),

    'log_channel' => env('LOG_CHANNEL', 'stack'),

    'temporary_files_root' => env('FFMPEG_TEMPORARY_FILES_ROOT', sys_get_temp_dir()),

    'temporary_files_encrypted_hls' => env(
        'FFMPEG_TEMPORARY_ENCRYPTED_HLS',
        env('FFMPEG_TEMPORARY_FILES_ROOT', sys_get_temp_dir()),
    ),
];
