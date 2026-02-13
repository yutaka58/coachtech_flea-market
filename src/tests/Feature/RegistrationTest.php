<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; // テスト毎にDBをリセット

use Illuminate\Support\Facades\Event;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '', // 名前を空にする
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // エラーがあるか確認
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /**
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => '', // メールアドレスを空にする
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // エラーがあるか確認
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '', // パスワードを空にする
            'password_confirmation' => '',
        ]);

        // エラーがあるか確認
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * パスワードが8文字以上で入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_password_characters_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'pass123', // パスワードを7文字以下にする
            'password_confirmation' => 'pass123',
        ]);

        // エラーがあるか確認
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * パスワードが確認用パスワードと一致していない場合、バリデーションメッセージが表示される
     */
    public function test_password_match_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456', // パスワードが一致しないようにする
        ]);

        // エラーがあるか確認
        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません']);
    }

    /**
     * 全ての項目が入力され、登録成功しプロフィール設定画面へ遷移する
     */
    public function test_registration_success_and_can_access_profile_after_verify()
    {
        Event::fake();

        // 1. 会員登録を実行
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // この時点では「メール認証」画面へ遷移する
        $response->assertRedirect('/email/verify');

        // 2. 登録済みユーザーを取得し、「認証済み」状態にする
        $user = \App\Models\User::where('email', 'test@example.com')->first();
        $user->markEmailAsVerified();

        // 3. 認証済みユーザーとして「プロフィール設定画面」にアクセス
        $response = $this->actingAs($user)->get('/profile/setup');
    }
}