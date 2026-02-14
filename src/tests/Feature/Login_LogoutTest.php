<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; // テスト毎にDBをリセット

class Login_LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 2:メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_required()
    {
        $response = $this->post('/login', [
            'email' => '', // メールアドレスを空にする
            'password' => 'password123',
        ]);

        // エラーがあるか確認
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * ID 2:パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_registration_required()
    {
        $response = $this->post('/login', [
            'email' => 'test2@example.com',
            'password' => 'password',
        ]);

        // エラーがあるか確認
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    /**
     * ID 2:登録情報と一致して場合、ログインされる
     */
    public function test_login_success()
    {
        // テスト用のユーザーを作成
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'), // パスワードを暗号化して保存
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/mypage/profile');

        $this->assertAuthenticatedAs($user);
    }

    /**
     * ID 3:ログアウトができる
     */
    public function test_logout_success()
    {
        $user = \App\Models\User::factory()->create();

        // ログイン状態でログアウトURLにアクセス
        $response = $this->actingAs($user)->post('/logout');

        // 未認証状態になったか
        $this->assertGuest();

        $response->assertRedirect('/login');
    }
}