<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Order::create([
            'user_id' => 1,      // 存在するユーザーID
            'item_id' => 1,   // SOLDにしたい商品のID
        ]);
    }
}
