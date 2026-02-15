<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

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

        // 3. データベースに商品が保存されているか
        $this->assertDatabaseHas('items', [
            'user_id'     => $user->id,
            'name'        => 'テスト商品名',
            'brand'       => 'テストブランド',
            'price'       => 5000,
            'description' => 'これはテスト商品の説明文です。',
        ]);

        // 4. カテゴリが紐付いているか（中間テーブルの確認）
        $this->assertDatabaseHas('category_item', [
            'category_id' => $category->id,
        ]);

        // 成功後のリダイレクト先を確認
        $response->assertStatus(302);
    }
}