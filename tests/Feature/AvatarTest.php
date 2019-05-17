<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    protected $cloudderMock;

    public function setup(): void
    {
        parent::setup();

        $this->authenticate();
    }

    /** @test */
    public function a_user_can_update_their_avatar()
    {
        Cloudder::spy();
        Cloudder::shouldReceive('getPublicId')->andReturn('avatars/12345');
        $fakeFile = UploadedFile::fake()->image('avatar.jpg', 700, 700);
        $response = $this->putJson('api/profile/avatar', [
            'avatar' => $fakeFile,
        ]);
        $imagePath = $response->getOriginalContent()->profile_image;

        Cloudder::shouldHaveReceived('upload')->with(Mockery::type('string'), Mockery::type('string'))->once();
        $this->assertEquals($imagePath, auth()->user()->profile_image);
    }
}
