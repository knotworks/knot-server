<?php

declare(strict_types=1);

namespace App\Services;

use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use RuntimeException;

class VideoProcessor
{
    /**
     * @return array{path: string, poster_path: string, width: int, height: int, mime_type: string, bytes: int, duration_seconds: int, extension: string}
     */
    public function process(string $sourcePath): array
    {
        $temporaryDirectory = dirname($sourcePath);
        $filesystem = app('filesystem')->createLocalDriver(['root' => $temporaryDirectory]);
        $media = FFMpeg::fromFilesystem($filesystem)->open(basename($sourcePath));
        $stream = $media->getVideoStream();

        if (! $stream || ! is_numeric($stream->get('width')) || ! is_numeric($stream->get('height'))) {
            throw new RuntimeException('Unable to probe video.');
        }

        $durationInMilliseconds = $media->getDurationInMiliseconds();
        $dimensions = $this->scaledDimensions(
            (int) $stream->get('width'),
            (int) $stream->get('height'),
            (int) config('media.max_video_dimension'),
        );
        $finalOutputPath = $this->temporaryFilePath('knot-video-', 'mp4', $temporaryDirectory);
        $finalPosterPath = $this->temporaryFilePath('knot-poster-', 'jpg', $temporaryDirectory);
        $format = (new X264('aac', 'libx264'))->setAdditionalParameters([
            '-preset', 'medium',
            '-crf', (string) config('media.video_crf'),
            '-pix_fmt', 'yuv420p',
            '-movflags', '+faststart',
        ]);

        $media->export()
            ->inFormat($format)
            ->resize($dimensions['width'], $dimensions['height'], ResizeFilter::RESIZEMODE_FIT, false)
            ->save(basename($finalOutputPath));

        FFMpeg::fromFilesystem($filesystem)->open(basename($finalOutputPath))
            ->getFrameFromSeconds($this->posterTimestamp($durationInMilliseconds))
            ->export()
            ->save(basename($finalPosterPath));

        return [
            'path' => $finalOutputPath,
            'poster_path' => $finalPosterPath,
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'mime_type' => 'video/mp4',
            'bytes' => filesize($finalOutputPath) ?: 0,
            'duration_seconds' => (int) ceil($durationInMilliseconds / 1000),
            'extension' => 'mp4',
        ];
    }

    private function temporaryFilePath(string $prefix, string $extension, string $directory): string
    {
        $temporaryPath = tempnam($directory, $prefix);

        if ($temporaryPath === false) {
            throw new RuntimeException('Unable to create temporary video outputs.');
        }

        $finalOutputPath = $temporaryPath . '.' . $extension;

        if (! rename($temporaryPath, $finalOutputPath)) {
            @unlink($temporaryPath);

            throw new RuntimeException('Unable to create temporary video outputs.');
        }

        return $finalOutputPath;
    }

    /**
     * @return array{width: int, height: int}
     */
    private function scaledDimensions(int $width, int $height, int $maxDimension): array
    {
        $longestEdge = max($width, $height);
        if ($longestEdge <= $maxDimension) {
            return [
                'width' => $width,
                'height' => $height,
            ];
        }

        $scale = $maxDimension / $longestEdge;

        return [
            'width' => max(2, ((int) round($width * $scale)) & ~1),
            'height' => max(2, ((int) round($height * $scale)) & ~1),
        ];
    }

    private function posterTimestamp(int $durationInMilliseconds): float
    {
        if ($durationInMilliseconds < 1000) {
            return 0.0;
        }

        return 1.0;
    }
}
