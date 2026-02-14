<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(100, 10000), // ランダムな金額を入れる
            'user_id' => \App\Models\User::factory(), // ユーザーも自動生成
            'description' => 'テスト用説明文',
            'img_url' => 'test_image.jpg',
            'condition' => '良好',
        ];
    }
}
