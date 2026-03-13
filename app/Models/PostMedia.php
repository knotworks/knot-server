<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\DeleteRemoteMediaAssets;
use App\Services\MediaUrlFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class PostMedia extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'url',
        'poster_url',
    ];

    protected $casts = [
        'processing_started_at' => 'datetime',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleted(function (self $media): void {
            $paths = array_filter([$media->path, $media->poster_path]);
            $temporaryPaths = array_filter([$media->temporary_path]);

            if ($paths !== [] || $temporaryPaths !== []) {
                DeleteRemoteMediaAssets::dispatch(
                    $media->disk ?: config('media.disk'),
                    $paths,
                    $temporaryPaths,
                );
            }
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getUrlAttribute(): ?string
    {
        return app(MediaUrlFactory::class)->postMediaUrl($this);
    }

    public function getPosterUrlAttribute(): ?string
    {
        return app(MediaUrlFactory::class)->postPosterUrl($this);
    }

    public function getFailureMessageAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Auth::id() === $this->post?->user_id ? $value : null;
    }
}
