<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $spam = [
            'name' => '腕時計',
            'cell' => '15000',
            'brand' => 'Rolex',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'condition' => '良好',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => 'HDD',
            'cell' => '5000',
            'brand' => '西芝',
            'description' => '高速で信頼性の高いハードディスク',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
            'condition' => '目立った傷や汚れなし',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => '玉ねぎ3束',
            'cell' => '300',
            'brand' => 'なし',
            'description' => '新鮮な玉ねぎ3束のセット',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            'condition' => 'やや傷や汚れあり',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => '革靴',
            'cell' => '4000',
            'description' => 'クラシックなデザインの革靴',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
            'condition' => '状態が悪い',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => 'ノートPC',
            'cell' => '45000',
            'description' => '高性能なノートパソコン',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
            'condition' => '良好',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => 'マイク',
            'cell' => '8000',
            'brand' => 'なし',
            'description' => '高音質のレコーディング用マイク',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
            'condition' => '目立った傷や汚れなし',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => 'ショルダーバッグ',
            'cell' => '3500',
            'description' => 'おしゃれなショルダーバッグ',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            'condition' => 'やや傷や汚れあり',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => 'タンブラー',
            'cell' => '500',
            'brand' => 'なし',
            'description' => '使いやすいタンブラー',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
            'condition' => '状態が悪い',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => 'コーヒーミル',
            'cell' => '4000',
            'brand' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'condition' => '良好',
        ];
        DB::table('products')->insert($spam);

        $spam = [
            'name' => 'メイクセット',
            'cell' => '2500',
            'description' => '便利なメイクアップセット',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'condition' => '目立った傷や汚れなし',
        ];
        DB::table('products')->insert($spam);

    }
}
