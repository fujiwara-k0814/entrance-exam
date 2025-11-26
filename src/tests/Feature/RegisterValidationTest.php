<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCannotRegisterWithoutName()
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->followRedirects($response)->assertSee('お名前を入力してください');
    }


    public function testUserCannotRegisterWithoutEmail()
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->followRedirects($response)->assertSee('メールアドレスを入力してください');
    }


    public function testUserCannotRegisterWithoutPassword()
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->followRedirects($response)->assertSee('パスワードを入力してください');
    }


    public function testUserCannotRegisterWithShortPassword()
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->followRedirects($response)->assertSee('パスワードは8文字以上で入力してください');
    }


    public function testUserCannotRegisterWithUnmatchedPassword()
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'unmathpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->followRedirects($response)->assertSee('パスワードと一致しません');
    }


    public function testUserCanRegisterWithValidData()
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users',[
            'name' => 'test',
            'email' => 'test@example.com',
        ]);
        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password', $user->password));
        
        $response->assertRedirect('/mypage/profile');
    }
}
