<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testVerificationEmailIsSentAfterUserRegistration()
    {
        //メール送信フェイク
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'postal_code' => null,
            'address' => null,
        ]);

        //通知機能発火
        $user->sendEmailVerificationNotification();

        //認証メールの送信確認
        Notification::assertSentTo(
            $user,
            \Illuminate\Auth\Notifications\VerifyEmail::class
        );
    }

    public function testVerificationPromptRedirectsToEmailVerificationSite()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
            'postal_code' => null,
            'address' => null,
        ]);

        $this->actingAs($user)->get('/email/verify')->assertStatus(200);

        $response = $this->actingAs($user)->get('http://localhost:8025');

        $response->assertRedirectContains('http://localhost:8025');
    }

    public function testEmailVerificationCompletionRedirectsToProfileSetup()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
            'postal_code' => null,
            'address' => null,
        ]);

        $this->actingAs($user);

        //署名パス取得
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirectContains('/mypage/profile');
    }
}