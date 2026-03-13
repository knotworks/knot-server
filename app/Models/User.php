<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\MediaUrlFactory;
use App\Traits\KnotFriendable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, KnotFriendable, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'avatar',
        'cover_image',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_name',
        'is_admin',
        'avatar_url',
    ];

    protected $casts = [
        'avatar_processing_started_at' => 'datetime',
        'avatar_processed_at' => 'datetime',
        'avatar_failed_at' => 'datetime',
    ];

    public function routeNotificationForTelegram()
    {
        return $this->telegram_user_id;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isAdmin(): bool
    {
        return $this->email === config('app.admin_email');
    }

    /**
     * Hash the user's password.
     *
     * @param  string  $value
     */
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getFullNameAttribute($value): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getIsAdminAttribute($value): bool
    {
        return $this->isAdmin();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return app(MediaUrlFactory::class)->avatarUrl($this);
    }

    public function getAvatarFailureMessageAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Auth::id() === $this->id ? $value : null;
    }

    /**
     * Fetch an activity feed for the given user.
     *
     *
     * @return Collection
     */
    public function timeline()
    {
        $ids = $this->getFriends()->map->id->prepend($this->id);

        return Post::with(Post::relationsForViewer($this))
            ->latest()
            ->whereIn('user_id', $ids)
            ->paginate(config('app.posts_per_page'));
    }

    public function feed()
    {
        return Post::with(Post::relationsForViewer($this))
            ->latest()
            ->where('user_id', $this->id)
            ->paginate(config('app.posts_per_page'));
    }

    public function getSuggestedFriends()
    {
        $ids = [$this->id];

        $this->getAllFriendships()->each(function ($friendship) use (&$ids) {
            array_push($ids, $friendship->sender_id, $friendship->recipient_id);
        });

        $idsToExclude = collect($ids)->unique()->values()->all();

        return self::whereNotIn('id', $idsToExclude)->get();
    }
}
