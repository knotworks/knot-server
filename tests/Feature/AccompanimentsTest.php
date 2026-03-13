<?php

declare(strict_types=1);

beforeEach(function (): void {
    $this->login();
});

it('allows a user to include accompaniments with a post', function (): void {
    $users = create('App\Models\User', [], 3);

    $postContent = [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        'accompaniments' => $users->map->id,
    ];

    $this->postJson('api/posts', $postContent)
        ->assertStatus(201)
        ->assertJson([
            'body' => $postContent['body'],
            'accompaniments' => $users->map(function ($user) {
                return ['user_id' => (string) $user['id']];
            })->toArray(),
        ]);
});

it('requires accompaniment ids to be numeric', function (): void {
    create('App\Models\User', [], 3);

    $postContent = [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        'accompaniments' => ['nope', 'noooo'],
    ];

    $this->postJson('api/posts', $postContent)->assertStatus(422);
});

it('requires accompaniment ids to be distinct', function (): void {
    $postContent = [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        'accompaniments' => [
            ['user_id' => 1],
            ['user_id' => 1],
        ],
    ];

    $this->postJson('api/posts', $postContent)->assertStatus(422);
});

it('requires accompaniment ids to match a user in the database', function (): void {
    create('App\Models\User', [], 1);

    $postContent = [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        'accompaniments' => [
            ['user_id' => 7],
        ],
    ];

    $this->postJson('api/posts', $postContent)->assertStatus(422);
});

it('prevents accompaniment ids from including the authenticated users id', function (): void {
    $postContent = [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        'accompaniments' => [
            ['user_id' => auth()->id()],
        ],
    ];

    $this->postJson('api/posts', $postContent)->assertStatus(422);
});
