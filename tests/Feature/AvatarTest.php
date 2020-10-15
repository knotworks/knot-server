<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_update_their_avatar()
    {
        $this->login();

        $avatarPath = 'testing/media/rX47fHdhjqwhL';
        $response = $this->putJson('api/profile/avatar', [
            'avatar' => $avatarPath,
        ]);

        $this->assertEquals($avatarPath, auth()->user()->avatar);
    }
}
