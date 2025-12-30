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
        \App\Models\User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('11111111'),
        ]);

        // 2. その後に商品を作成する
        $this->call(ProductTableSeeder::class);
        $this->call(OrderTableSeeder::class);
    }
}
