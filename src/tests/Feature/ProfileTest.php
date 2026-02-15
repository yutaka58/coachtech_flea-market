<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 12: 配送先変更機能のテスト
     */
    public function test_user_can_update_address_and_it_is_reflected_in_purchase()
    {
        // 1. 準備：ユーザーと商品
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        // 2. 住所変更を実行
        $newAddressData = [
            'post_code' => '999-9999',
            'address'   => '東京都新宿区新大久保',
            'building'  => 'テストマンション101',
        ];

        $response = $this->actingAs($user)->post(route('address.update', ['item_id' => $item->id]), $newAddressData);

        // 3. 商品購入画面を再度開き、新しい住所が表示されているか
        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertSee('999-9999');
        $response->assertSee('東京都新宿区新大久保');
        $response->assertSee('テストマンション101');

        // 4. 購入を実行し、購入データに変更後の住所が紐づいているか確認
        $this->actingAs($user)->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'card',
            'post_code'      => '999-9999',
            'address'        => '東京都新宿区新大久保',
            'building'       => 'テストマンション101',
        ]);

        // 購入成功ページを叩く（DB保存をトリガー）
        $this->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]));

        // 5. ordersテーブルに「変更後の住所」が記録されているか確認
        $this->assertDatabaseHas('orders', [
            'user_id'   => $user->id,
            'item_id'   => $item->id,
            'post_code' => '999-9999',
            'address'   => '東京都新宿区新大久保',
            'building'  => 'テストマンション101',
        ]);
    }

    /**
     * ID 13: プロフィールページで必要な情報が取得・表示される
     */
    public function test_user_profile_displays_correct_info()
    {
        // 1. 準備：ユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'image' => 'test_profile.png',
            'email_verified_at' => now(),
        ]);

        // 2. 準備：テストユーザーが「出品した商品」を作成
        $exhibitedItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品した商品A',
        ]);

        // 3. 準備：テストユーザーが「購入した商品」を作成
        $anotherUser = User::factory()->create();
        $purchasedItem = Item::factory()->create(['name' => '購入した商品B']);

        // ordersテーブルに記録
        Order::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'post_code' => '123-4567',
            'address'   => '東京都渋谷区',
            'building'  => 'テストビル',
        ]);

        // 4. 実行：マイページを表示（デフォルト：出品した商品一覧）
        $this->actingAs($user);
        $response = $this->get('/mypage');

        // 名前と画像が表示されているか
        $response->assertSee('テストユーザー');
        $response->assertSee('test_profile.png');
        // 出品した商品が表示されているか
        $response->assertSee('出品した商品A');

        // 5. 実行：購入した商品タブを表示
        $response = $this->get('/mypage?page=buy');

        // 購入した商品が表示されているか
        $response->assertSee('購入した商品B');
    }

    /**
     * ID 14: プロフィール設定画面に変更項目が初期値としてセットされている
     */
    public function test_profile_edit_screen_shows_default_values()
    {
        // 1. 準備：既存の情報を持つユーザーを作成
        $user = User::factory()->create([
            'name' => '既存の名前',
            'image' => 'existing_image.png',
            'post_code' => '111-1111',
            'address' => '東京都新宿区',
            'building' => '既存ビル101',
            'email_verified_at' => now(),
        ]);

        // 2. 実行：プロフィール編集画面を表示
        $response = $this->actingAs($user)->get('/mypage/profile');

        // 3. 各入力項目の value 属性に情報が入っているか
        $response->assertSee('既存の名前');
        $response->assertSee('111-1111');
        $response->assertSee('東京都新宿区');
        $response->assertSee('既存ビル101');

        // 画像は <img> タグの src や、ファイル名が表示されているか確認
        $response->assertSee('existing_image.png');
    }

    /**
     * ID 15: 商品出品画面にて必要な情報が保存できる
     */
    public function test_user_can_list_new_item()
    {
        // 1. 準備：ユーザーとカテゴリ、商品の状態
        $user = User::factory()->create(['email_verified_at' => now()]);
        $category = Category::create(['name' => 'ファッション']);

        // ストレージのフェイク（画像を扱う場合）
        Storage::fake('public');
        $image = UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg');

        // 2. 出品リクエストの送信
        $itemData = [
            'category_id' => [$category->id],
            'condition'    => '良好',
            'name'         => 'テスト商品名',
            'brand'        => 'テストブランド',
            'description'  => 'これはテスト商品の説明文です。',
            'price'        => 5000,
            'image'        => $image,
        ];

        // ルート名は sell.store または item.store に合わせてください
        $response = $this->actingAs($user)->post('/sell', $itemData);

        // 3. 検証：データベースに商品が保存されているか
        $this->assertDatabaseHas('items', [
            'user_id'     => $user->id,
            'name'        => 'テスト商品名',
            'brand'       => 'テストブランド',
            'price'       => 5000,
            'description' => 'これはテスト商品の説明文です。',
        ]);

        // 4. 検証：カテゴリが紐付いているか（中間テーブルの確認）
        // テーブル名は item_category など、あなたの設計に合わせてください
        $this->assertDatabaseHas('category_item', [
            'category_id' => $category->id,
        ]);

        // 成功後のリダイレクト先を確認
        $response->assertStatus(302);
    }
}