<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'condition_id' => '1',
                'image_path' => 'storage/item_images/Armani+Mens+Clock.jpg', 
                'name' => '腕時計', 
                'brand' => 'Rolax',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計', 
                'categories' => [1, 5, 12],
            ],
            [
                'condition_id' => '2',
                'image_path' => 'storage/item_images/HDD+Hard+Disk.jpg',
                'name' => 'HDD',
                'brand' => '西芝',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'categories' => [2],
            ],
            [
                'condition_id' => '3',
                'image_path' => 'storage/item_images/iLoveIMG+d.jpg',
                'name' => '玉ねぎ3束',
                'brand' => 'なし',
                'price' => '300',
                'description' => '新鮮な玉ねぎ3束のセット',
                'categories' => [10],
            ],
            [
                'condition_id' => '4',
                'image_path' => 'storage/item_images/Leather+Shoes+Product+Photo.jpg',
                'name' => '革靴',
                'brand' => '',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'categories' => [1, 5],
            ],
            [
                'condition_id' => '1',
                'image_path' => 'storage/item_images/Living+Room+Laptop.jpg',
                'name' => 'ノートPC',
                'brand' => '',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'categories' => [2],
            ],
            [
                'condition_id' => '2',
                'image_path' => 'storage/item_images/Music+Mic+4632231.jpg',
                'name' => 'マイク',
                'brand' => 'なし',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'categories' => [2],
            ],
            [
                'condition_id' => '3',
                'image_path' => 'storage/item_images/Purse+fashion+pocket.jpg',
                'name' => 'ショルダーバッグ',
                'brand' => '',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'categories' => [1, 4],
            ],
            [
                'condition_id' => '4',
                'image_path' => 'storage/item_images/Tumbler+souvenir.jpg',
                'name' => 'タンブラー',
                'brand' => 'なし',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'categories' => [10],
            ],
            [
                'condition_id' => '1',
                'image_path' => 'storage/item_images/Waitress+with+Coffee+Grinder.jpg',
                'name' => 'コーヒーミル',
                'brand' => 'Starbacks',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
                'categories' => [10],
            ],
            [
                'condition_id' => '2',
                'image_path' => 'storage/item_images/外出メイクアップセット.jpg',
                'name' => 'メイクセット',
                'brand' => '',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'categories' => [1, 4, 6],
            ],
        ];

        foreach ($items as $itemDefinition) {
            $categoryIds = $itemDefinition['categories'];
            unset($itemDefinition['categories']);

            $item = Item::create($itemDefinition);
            $item->categories()->sync($categoryIds);
        }
    }
}
