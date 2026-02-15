<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Category;
use App\Models\Comment;

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

        // 2. 他人が出品した商品を作成（まだ売れていない）
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品',
        ]);

        // 3. 他人が出品した商品を作成（売れている商品にする）
        $soldItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '売り切れ商品',
        ]);

        // 4. 【重要】この商品に紐づく Order（注文）を作成する
        \App\Models\Order::create([
            'item_id' => $soldItem->id,
            'user_id' => $me->id, // 誰が買ったかは問わないので自分でもOK
            'post_code' => '123-4567',
            'address'   => '東京都渋谷区...',
            'building'  => 'テストビル',
        ]);

        // 5. 実行：自分としてログインして商品一覧ページを開く
        $response = $this->actingAs($me)->get('/');

        // 6. 検証：すべての商品（他人のもの）が表示されているか
        $response->assertSee('他人の商品');
        $response->assertSee('売り切れ商品');

        // 7. 検証：購入済み商品に「Sold」ラベルが表示されているか
        $response->assertSee('SOLD');

        // 8. 検証：自分が出品した商品が表示「されていない」こと
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
        $response = $this->actingAs($me)->get('/?tab=mylist');

        // 4. 検証：いいねした商品だけが表示されているか
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

    /**
     * ID 6:部分一致する商品が表示される
     */
    public function test_search_product()
    {
        // 1. 準備：検索にヒットする商品としない商品を作成
        Item::factory()->create(['name' => 'テスト用腕時計']);
        Item::factory()->create(['name' => '高級な時計']);
        Item::factory()->create(['name' => '関係ない商品']);

        // 2. 実行：キーワード「時計」で検索
        $response = $this->get('/?keyword=時計');

        // 3. 検証：部分一致する商品だけが表示され、関係ないものは表示されない
        $response->assertStatus(200);
        $response->assertSee('テスト用腕時計');
        $response->assertSee('高級な時計');
        $response->assertDontSee('関係ない商品');
    }

    /**
     * ID 6: 検索状態がマイリストでも保持されている
     */
    public function test_search_keyword_mylist_tab()
    {
        // 1. 準備：ユーザー作成とログイン
        $user = User::factory()->create();

        // 2. 実行：まずキーワード「時計」で検索した状態のトップページへ
        $searchUrl = '/?keyword=時計';
        $response = $this->actingAs($user)->get($searchUrl);

        // 3. 実行：次にマイリストタブ（tab=mylist）に遷移する
        $mylistUrlWithKeyword = '/?tab=mylist&keyword=時計';
        $responseMylist = $this->actingAs($user)->get($mylistUrlWithKeyword);

        // 4. 検証：マイリストページでも検索窓（input）に「時計」が残っているか
        $responseMylist->assertSee('value="時計"', false);
    }

    /**
     * ID 7: 商品詳細情報がすべて表示される
     */
    public function test_item_detail_page_displays_all_information()
    {
        // 1. 準備：ユーザーとカテゴリ
        $user = User::factory()->create(['name' => '出品者']);
        $categories = Category::factory()->count(2)->create([
            'name' => 'テストカテゴリ'
        ]);

        // 2. 準備：商品を作成
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 5000,
            'description' => 'これは詳細な商品説明です。',
            'condition' => '良好',
        ]);

        // カテゴリを紐付け（多対多のリレーションを想定）
        $item->categories()->attach($categories->pluck('id'));

        // 3. 準備：コメントを追加
        $commentUser = User::factory()->create(['name' => 'コメントユーザー']);
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'comment' => '素敵な商品ですね！',
        ]);

        // 4. 実行：商品詳細ページへアクセス
        $response = $this->get("/item/{$item->id}");

        // 5. 検証：基本情報
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('5,000');
        $response->assertSee('これは詳細な商品説明です。');
        $response->assertSee('良好');

        // 6. 検証：複数カテゴリ
        foreach ($categories as $category) {
            $response->assertSee($category->content); // カテゴリ名が表示されているか
        }

        // 7. 検証：コメント情報
        $response->assertSee('コメントユーザー');
        $response->assertSee('素敵な商品ですね！');

        // 8. 検証：統計数（いいね数・コメント数）
        $response->assertSee('1'); // コメント数
    }
}
