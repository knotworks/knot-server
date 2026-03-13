<?php

declare(strict_types=1);

use App\Contracts\LinkMetaService;
use App\Services\OpenGraphLinkMetaService;
use Mockery\MockInterface;

beforeEach(function (): void {
    $this->login();
    $this->fakeResponse = ['title' => 'Syropia', 'description' => 'An on-line website', 'image' => 'foo.jpg'];

    $this->instance(
        LinkMetaService::class,
        Mockery::mock(OpenGraphLinkMetaService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetch')->andReturn($this->fakeResponse);
        }),
    );
});

it('can use the opengraph service to fetch a links meta info', function (): void {
    $this->postJson('api/services/link-meta', [
        'url' => 'https://format.com',
    ])->assertStatus(200)->assertJson($this->fakeResponse);
});

it('requires a valid url', function (): void {
    $this->postJson('api/services/link-meta', [])->assertStatus(422);
    $this->postJson('api/services/link-meta', ['url' => 'htf:/bad.url'])->assertStatus(422);
});
