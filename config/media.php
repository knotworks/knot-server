<?php

declare(strict_types=1);

return [
    'disk' => env('MEDIA_DISK', 'r2'),
    'temporary_disk' => env('MEDIA_TEMPORARY_DISK', 'media-temp'),
    'url_ttl_minutes' => (int) env('MEDIA_URL_TTL_MINUTES', 15),
    'max_files_per_post' => (int) env('MEDIA_MAX_FILES_PER_POST', 5),
    'max_image_kb' => (int) env('MEDIA_MAX_IMAGE_KB', 10240),
    'max_video_kb' => (int) env('MEDIA_MAX_VIDEO_KB', 102400),
    'max_image_dimension' => (int) env('MEDIA_MAX_IMAGE_DIMENSION', 2560),
    'max_video_dimension' => (int) env('MEDIA_MAX_VIDEO_DIMENSION', 1080),
    'image_quality' => (int) env('MEDIA_IMAGE_QUALITY', 82),
    'cleanup_after_hours' => (int) env('MEDIA_CLEANUP_AFTER_HOURS', 24),
    'video_crf' => (int) env('MEDIA_VIDEO_CRF', 23),
    'ffmpeg_binary' => env('FFMPEG_BINARY', 'ffmpeg'),
    'ffprobe_binary' => env('FFPROBE_BINARY', 'ffprobe'),
];
