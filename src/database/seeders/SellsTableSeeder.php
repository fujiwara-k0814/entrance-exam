<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sell;

class SellsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['item_id' => '1', 'user_id' => '1'],
            ['item_id' => '2', 'user_id' => '1'],
            ['item_id' => '3', 'user_id' => '1'],
            ['item_id' => '4', 'user_id' => '1'],
            ['item_id' => '5', 'user_id' => '1'],
            ['item_id' => '6', 'user_id' => '2'],
            ['item_id' => '7', 'user_id' => '2'],
            ['item_id' => '8', 'user_id' => '2'],
            ['item_id' => '9', 'user_id' => '2'],
            ['item_id' => '10', 'user_id' => '2'],
        ];

        foreach ($items as $item) {
            Sell::create($item);
        }
    }
}
