<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 4: 全商品を取得できる
     * 購入済み商品は「Sold」と表示される
     * 自分が出品した商品は表示されない
     */
    public function test_item_index_display_logic()
    {
        // 1. 準備：ユーザーを2人作成（自分と他人）
        $me = User::factory()->create();
        $otherUser = User::factory()->create();

        // 1. 他人が出品した商品を作成（まだ売れていない）
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品',
        ]);

        // 2. 他人が出品した商品を作成（売れている商品にする）
        $soldItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '売り切れ商品',
        ]);

        // 3. 【重要】この商品に紐づく Order（注文）を作成する
        \App\Models\Order::create([
            'item_id' => $soldItem->id,
            'user_id' => $me->id, // 誰が買ったかは問わないので自分でもOK
            'post_code' => '123-4567',   // ← これを追加
            'address'   => '東京都渋谷区...', // おそらくこれも必須
            'building'  => 'テストビル',     // これも必要かもしれません
        ]);

        // 3. 実行：自分としてログインして商品一覧ページを開く
        $response = $this->actingAs($me)->get('/');

        // 5. 検証：すべての商品（他人のもの）が表示されているか
        $response->assertSee('他人の商品');
        $response->assertSee('売り切れ商品');

        // 6. 検証：購入済み商品に「Sold」ラベルが表示されているか
        $response->assertSee('SOLD');

        // 7. 検証：自分が出品した商品が表示「されていない」こと
        $response->assertDontSee('自分の商品');
    }


    /**
     * ID 5: マイリスト一覧取得のテスト
     */
    public function test_mylist_display_logic()
    {
        // 1. 準備：自分と商品を作成
        $me = User::factory()->create();
        $itemA = Item::factory()->create(['name' => 'いいねした商品']);
        $itemB = Item::factory()->create(['name' => 'いいねしていない商品']);
        $soldItem = Item::factory()->create(['name' => '売り切れの商品']);

        // 2. 準備：リレーション（いいねと購入）を作成
        // 商品Aに「いいね」をする（中間テーブルにデータをいれる）
        $me->favoriteItems()->attach($itemA->id);
        
        // 売り切れ商品にも「いいね」をして、さらに注文データを作る
        $me->favoriteItems()->attach($soldItem->id);
        Order::create([
            'item_id' => $soldItem->id,
            'user_id' => User::factory()->create()->id, // 他人が買った
            'post_code' => '123-4567',
            'address' => 'テスト住所',
            'building'  => 'テストビル', 
        ]);

        // 3. 実行：ログインしてマイリストページを開く
        // ※URLが /?tab=mylist の場合はそのように指定します
        $response = $this->actingAs($me)->get('/?tab=mylist');

        // 4. 検証：いいねした商品だけが表示されているか
        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertSee('売り切れの商品');
        $response->assertDontSee('いいねしていない商品');

        // 5. 検証：購入済み商品に「SOLD」ラベルが表示されているか
        $response->assertSee('SOLD');
    }

    /**
     * ID 5:未認証の場合は何も表示されない
     */
    public function test_guest_cannot_see_mylist()
    {
        // ログインせずにマイリストページへ
        $response = $this->get('/?tab=mylist');

        // 何も表示されない
        $response->assertRedirect('/login');
    }
}
