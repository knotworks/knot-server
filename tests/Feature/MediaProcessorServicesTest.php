<?php

declare(strict_types=1);

use App\Services\ImageProcessor;
use App\Services\VideoProcessor;
use Symfony\Component\Process\Process;

it('processes jpeg images with resize and jpeg output', function (): void {
    $sourcePath = temporaryFilePath('source-image-', 'jpg');
    $image = imagecreatetruecolor(3200, 2400);

    if ($image === false) {
        throw new RuntimeException('Unable to create test image.');
    }

    try {
        imagefill($image, 0, 0, imagecolorallocate($image, 12, 34, 56));
        imagejpeg($image, $sourcePath, 90);
        imagedestroy($image);

        $processed = app(ImageProcessor::class)->process($sourcePath);

        expect($processed['extension'])->toBe('jpg');
        expect($processed['mime_type'])->toBe('image/jpeg');
        expect($processed['width'])->toBe(2560);
        expect($processed['height'])->toBe(1920);
        expect($processed['bytes'])->toBeGreaterThan(0);
        expect(file_exists($processed['path']))->toBeTrue();
    } finally {
        @unlink($sourcePath);
        @unlink($processed['path'] ?? '');
    }
});

it('preserves png output format', function (): void {
    $sourcePath = temporaryFilePath('source-image-', 'png');
    $image = imagecreatetruecolor(1200, 800);

    if ($image === false) {
        throw new RuntimeException('Unable to create test image.');
    }

    try {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 255, 0, 0));
        imagepng($image, $sourcePath, 9);
        imagedestroy($image);

        $processed = app(ImageProcessor::class)->process($sourcePath);

        expect($processed['extension'])->toBe('png');
        expect($processed['mime_type'])->toBe('image/png');
        expect($processed['width'])->toBe(1200);
        expect($processed['height'])->toBe(800);
        expect(file_exists($processed['path']))->toBeTrue();
    } finally {
        @unlink($sourcePath);
        @unlink($processed['path'] ?? '');
    }
});

it('processes videos to mp4 and creates a jpeg poster', function (): void {
    $sourcePath = temporaryFilePath('source-video-', 'mp4');
    $process = new Process([
        'ffmpeg',
        '-y',
        '-f',
        'lavfi',
        '-i',
        'testsrc=size=1600x900:rate=30',
        '-f',
        'lavfi',
        '-i',
        'sine=frequency=1000:sample_rate=44100',
        '-t',
        '2',
        '-c:v',
        'libx264',
        '-pix_fmt',
        'yuv420p',
        '-c:a',
        'aac',
        $sourcePath,
    ]);

    $process->mustRun();

    try {
        $processed = app(VideoProcessor::class)->process($sourcePath);
        $posterDimensions = getimagesize($processed['poster_path']);

        expect($processed['extension'])->toBe('mp4');
        expect($processed['mime_type'])->toBe('video/mp4');
        expect($processed['width'])->toBe(1080);
        expect($processed['height'])->toBe(608);
        expect($processed['duration_seconds'])->toBe(2);
        expect($processed['bytes'])->toBeGreaterThan(0);
        expect(file_exists($processed['path']))->toBeTrue();
        expect(file_exists($processed['poster_path']))->toBeTrue();
        expect($posterDimensions)->not()->toBeFalse();
        expect($posterDimensions[0])->toBe(1080);
        expect($posterDimensions[1])->toBe(608);
        expect($posterDimensions['mime'])->toBe('image/jpeg');
    } finally {
        @unlink($sourcePath);
        @unlink($processed['path'] ?? '');
        @unlink($processed['poster_path'] ?? '');
    }
});

function temporaryFilePath(string $prefix, string $extension): string
{
    $temporaryPath = tempnam(sys_get_temp_dir(), $prefix);

    if ($temporaryPath === false) {
        throw new RuntimeException('Unable to create temporary test file.');
    }

    $finalPath = $temporaryPath . '.' . $extension;

    if (! rename($temporaryPath, $finalPath)) {
        @unlink($temporaryPath);

        throw new RuntimeException('Unable to create temporary test file.');
    }

    return $finalPath;
}
