<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaceVerifyTest extends TestCase
{
    use RefreshDatabase;

    public function test_face_verify_accepts_base64_image_and_returns_success(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $oneByOnePng = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMB/az9VQAAAABJRU5ErkJggg==';
        $dataUrl = 'data:image/png;base64,' . $oneByOnePng;

        $response = $this->postJson(route('kyc.faceVerify'), [
            'image' => $dataUrl,
        ]);

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'status' => 'uploaded',
        ]);
    }
}

