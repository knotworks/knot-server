<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Locatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;

class Post extends Model
{
    use HasFactory, Locatable;

    protected $guarded = [];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (self $model): void {
            $model->accompaniments->each->delete();
            $model->reactions->each->delete();
            $model->comments->each->delete();
            $model->media->each->delete();
        });
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    /**
     * @return array<string, mixed>
     */
    public static function relationsForViewer(?User $viewer = null): array
    {
        return [
            'location',
            'user',
            'comments',
            'reactions.user',
            'accompaniments.user',
            'media' => static function (Relation $query) use ($viewer): void {
                if (! $viewer) {
                    $query->where('status', 'ready');

                    return;
                }

                $query
                    ->where(function (Builder $builder) use ($viewer): void {
                        $builder->where('status', 'ready')
                            ->orWhereHas('post', function (Builder $postQuery) use ($viewer): void {
                                $postQuery->where('user_id', $viewer->id);
                            });
                    })
                    ->orderBy('sort_order');
            },
        ];
    }

    /**
     * Fetch the associated user for the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fetch the associated accompaniments for the post.
     */
    public function accompaniments(): HasMany
    {
        return $this->hasMany(Accompaniment::class);
    }

    /**
     * Fetch the associated reactions for the post.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Fetch the associated comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Attach accompaniments to a post.
     */
    public function addAccompaniments($accompaniments): mixed
    {
        return $this->accompaniments()->createMany($accompaniments);
    }

    /**
     * Attach accompaniments to a post.
     */
    public function addReaction($reaction): mixed
    {
        return $this->reactions()->create($reaction);
    }

    /**
     * Attach accompaniments to a post.
     */
    public function addComment($comment): mixed
    {
        return $this->comments()->create($comment);
    }
}
