<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Knot\Contracts\LinkMetaService;
use Knot\Services\OpenGraphLinkMetaService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class OpenGraphLinkMetaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $fakeResponse;

    public function setup(): void
    {
        parent::setup();

        $this->login();
        $this->fakeResponse = ['title' => 'Syropia', 'description' => 'An on-line website', 'image' => 'foo.jpg'];

        $this->instance(
            LinkMetaService::class,
            Mockery::mock(OpenGraphLinkMetaService::class, function (MockInterface $mock) {
                $mock->shouldReceive('fetch')->andReturn($this->fakeResponse);
            }),
        );
    }

    /** @test */
    public function can_use_the_opengraph_service_to_fetch_a_links_meta_info()
    {
        $this->postJson('api/services/link-meta', [
            'url' => 'https://format.com',
        ])->assertStatus(200)->assertJson($this->fakeResponse);
    }

    /** @test */
    public function a_valid_url_is_required()
    {
        $this->postJson('api/services/link-meta', [])->assertStatus(422);

        $this->postJson('api/services/link-meta', ['url' => 'htf:/bad.url'])->assertStatus(422);
    }
}
