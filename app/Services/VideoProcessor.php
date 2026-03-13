<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use Symfony\Component\Process\Process;

class VideoProcessor
{
    /**
     * @return array{path: string, poster_path: string, width: int, height: int, mime_type: string, bytes: int, duration_seconds: int, extension: string}
     */
    public function process(string $sourcePath): array
    {
        $outputPath = tempnam(sys_get_temp_dir(), 'knot-video-');
        $posterPath = tempnam(sys_get_temp_dir(), 'knot-poster-');

        if ($outputPath === false || $posterPath === false) {
            throw new RuntimeException('Unable to create temporary video outputs.');
        }

        $finalOutputPath = $outputPath . '.mp4';
        $finalPosterPath = $posterPath . '.jpg';

        rename($outputPath, $finalOutputPath);
        rename($posterPath, $finalPosterPath);

        $probe = $this->probe($sourcePath);
        $dimensions = $this->scaledDimensions(
            $probe['width'],
            $probe['height'],
            (int) config('media.max_video_dimension'),
        );

        $process = new Process([
            (string) config('media.ffmpeg_binary'),
            '-y',
            '-i',
            $sourcePath,
            '-vf',
            'scale=' . $dimensions['width'] . ':' . $dimensions['height'] . ':force_original_aspect_ratio=decrease',
            '-c:v',
            'libx264',
            '-preset',
            'medium',
            '-crf',
            (string) config('media.video_crf'),
            '-pix_fmt',
            'yuv420p',
            '-c:a',
            'aac',
            '-movflags',
            '+faststart',
            $finalOutputPath,
        ]);

        $process->mustRun();

        $posterProcess = new Process([
            (string) config('media.ffmpeg_binary'),
            '-y',
            '-ss',
            '00:00:01',
            '-i',
            $finalOutputPath,
            '-frames:v',
            '1',
            '-q:v',
            '2',
            $finalPosterPath,
        ]);

        $posterProcess->mustRun();

        return [
            'path' => $finalOutputPath,
            'poster_path' => $finalPosterPath,
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'mime_type' => 'video/mp4',
            'bytes' => filesize($finalOutputPath) ?: 0,
            'duration_seconds' => (int) ceil($probe['duration']),
            'extension' => 'mp4',
        ];
    }

    /**
     * @return array{width: int, height: int, duration: float}
     */
    private function probe(string $sourcePath): array
    {
        $probeProcess = new Process([
            (string) config('media.ffprobe_binary'),
            '-v',
            'error',
            '-select_streams',
            'v:0',
            '-show_entries',
            'stream=width,height:format=duration',
            '-of',
            'json',
            $sourcePath,
        ]);

        $probeProcess->mustRun();

        $payload = json_decode($probeProcess->getOutput(), true);

        $stream = $payload['streams'][0] ?? null;
        $duration = $payload['format']['duration'] ?? null;

        if (! is_array($stream) || ! is_numeric($stream['width'] ?? null) || ! is_numeric($stream['height'] ?? null) || ! is_numeric($duration)) {
            throw new RuntimeException('Unable to probe video.');
        }

        return [
            'width' => (int) $stream['width'],
            'height' => (int) $stream['height'],
            'duration' => (float) $duration,
        ];
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
}
