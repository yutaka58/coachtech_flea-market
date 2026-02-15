<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}