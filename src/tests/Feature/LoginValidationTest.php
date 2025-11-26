<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCannotLoginWithoutEmail()
    {
        $this->get('/login')->assertStatus(200);

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->followRedirects($response)->assertSee('メールアドレスを入力してください');
    }


    public function testUserCannotLoginWithoutPassword()
    {
        $this->get('/login')->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->followRedirects($response)->assertSee('パスワードを入力してください');
    }


    public function testUserCannotLoginWithWrongCredentials()
    {
        User::factory()->create([
            'email' => 'wrong_test@example.com',
            'password' => bcrypt('wrong_password'),
        ]);

        $this->get('/login')->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->followRedirects($response)->assertSee('ログイン情報が登録されていません');
    }


    public function testUserCanLoginWithValidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->get('/login')->assertStatus(200);

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }
}
