<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class UserInformationChangeFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserEditFormDisplaysPreviouslySetValues()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'image_path' => 'storage/profile_images/test_image.png',
            'name' => 'test',
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'address' => 'address',
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile')->assertStatus(200);
        $response->assertSee($user->image_path);
        $response->assertSee($user->name);
        $response->assertSee($user->postal_code);
        $response->assertSee($user->address);
    }
}
