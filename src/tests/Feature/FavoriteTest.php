<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 8: いいねアイコンを押下して登録・解除ができる
     */
    public function test_user_can_toggle_favorite()
    {
        // 1. 準備：ユーザー（認証済み）と商品
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        // 2. いいね登録（POSTリクエスト）
        $response = $this->actingAs($user)->post("/products/{$item->id}/favorite");
        $response->assertStatus(200);

        // 3. 詳細ページを表示して「ピンクのハート」があるか確認
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('ハートロゴ_ピンク.png'); // クラス名ではなく画像名でチェック
        $response->assertSee('1'); // カウントが1であること

        // 4. いいね解除
        $response = $this->actingAs($user)->post("/products/{$item->id}/favorite");
        $response->assertStatus(200);

        // 5. 詳細ページを表示して「デフォルト（黒/白）のハート」に戻ったか確認
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('ハートロゴ_デフォルト.png');
        $response->assertSee('0'); // カウントが0に戻っていること
    }
}