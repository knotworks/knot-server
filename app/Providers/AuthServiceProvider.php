<?php

namespace Knot\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Knot\Models\Post::class => \Knot\Policies\PostPolicy::class,
        \Knot\Models\Comment::class => \Knot\Policies\CommentPolicy::class,
        \Knot\Models\User::class => \Knot\Policies\ViewProfilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
