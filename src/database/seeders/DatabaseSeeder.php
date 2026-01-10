<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. カテゴリーなどのマスターデータ（商品が依存するもの）
        $this->call([
            CategoryTableSeeder::class,
        ]);

        // 2. テストユーザー（重複を避ける書き方）
        \App\Models\User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'test',
                'password' => bcrypt('password'),
            ]
        );

        // 3. その他、商品や注文など
        $this->call([
            ItemTableSeeder::class,

        ]);
    }
}
