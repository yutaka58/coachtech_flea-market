<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 9: ログイン済みのユーザーはコメントを送信できる
     */
    public function test_logged_in_user_can_send_comment()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        // 1. ログインしてコメントを送信
        $commentData = ['comment' => 'テストコメントです'];
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", $commentData);

        // 2. DBに保存されているか、詳細ページに表示されているか
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => 'テストコメントです',
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertSee('テストコメントです');
        $response->assertSee('1'); // コメント数が増加
    }

    /**
     * ID 9: ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_user_cannot_send_comment()
    {
        $item = Item::factory()->create();
        $commentData = ['comment' => 'ログインしてないコメント'];

        // ログインせずにPOST
        $response = $this->post("/item/{$item->id}/comment", $commentData);

        // ログイン画面へリダイレクトされることを確認
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', ['comment' => 'ログインしてないコメント']);
    }

    /**
     * ID 9: コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_is_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        // 空のコメントを送信
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", ['comment' => '']);

        // セッションにエラーがあることを確認
        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * ID 9: コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_max_length()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        // 256文字のコメントを作成
        $longComment = str_repeat('あ', 256);
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", ['comment' => $longComment]);

        $response->assertSessionHasErrors(['comment']);
    }
}