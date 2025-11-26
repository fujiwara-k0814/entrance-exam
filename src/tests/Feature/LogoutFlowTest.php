<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanLogoutSuccessfully()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this->actingAs($user)->assertAuthenticated();

        $this->post('/logout');
        
        $this->assertGuest();
    }
}
