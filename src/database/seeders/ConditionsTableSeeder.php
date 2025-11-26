<?php

namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Seeder;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conditions = [
            ['id' => 1, 'name' => '良好'],
            ['id' => 2, 'name' => '目立った傷や汚れなし'],
            ['id' => 3, 'name' => 'やや傷や汚れあり'],
            ['id' => 4, 'name' => '状態が悪い']
        ];

        foreach ($conditions as $condition) {
            Condition::create($condition);
        }
    }
}
