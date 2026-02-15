<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 16: 会員登録後のメール送信と認証のテスト
     */
    public function test_email_verification_flow()
    {
        // 1. メール送信のフェイクを準備
        Notification::fake();

        // 2. 会員登録を実行
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        // 会員登録後にユーザーが作成され、メールが送信されたか確認
        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);

        // 3. メール認証誘導画面が表示されるか
        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertSee('認証メールを送付しました');

        // 4. 認証リンクをクリック
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        // 5. 認証完了後にプロフィール設定画面へ遷移するか
        $response->assertRedirect('/mypage/profile');
    }
}