<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID 10: 商品購入機能のテスト
     */
    public function test_user_can_purchase_item_and_status_changes()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 1000]);

        // 1. 購入が完了する
        $response = $this->actingAs($buyer)->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'card',
            'post_code'      => '123-4567',
            'address'        => '東京都渋谷区',
            'building'       => 'テストビル'
        ]);

        // 2. 決済成功ページへ
        $this->actingAs($buyer)->get(route('purchase.success', ['item_id' => $item->id]));

        // 3. 購入した商品が「sold」として表示されている
        $response = $this->get('/');
        $response->assertSee('sold');

        // 4. 購入した商品がプロフィールの購入した商品一覧に追加されている
        $response = $this->get('/mypage?page=buy');
        $response->assertSee($item->name);
    }

    /**
     * ID 11: 小計画面で支払い方法の変更が反映される
     */
    public function test_payment_method_selection_is_reflected_in_purchase_page()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        // 2. 支払い方法を選択するリクエストを送る
        $response = $this->actingAs($user)->post(route('payment.save_session', ['item_id' => $item->id]), [
            'payment_method' => 'konbini',
        ]);

        // 3. 購入画面を再度表示し、選択した「コンビニ払い」が表示されているか確認
        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));

        $response->assertSee('コンビニ払い');

        // 4. 支払い方法を選択するリクエストを送る
        $response = $this->actingAs($user)->post(route('payment.save_session', ['item_id' => $item->id]), [
            'payment_method' => 'card',
        ]);

        // 5. 購入画面を再度表示し、選択した「カード支払い」が表示されているか確認
        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));

        $response->assertSee('カード支払い');
    }
}