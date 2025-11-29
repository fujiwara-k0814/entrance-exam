<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryAddress;

class DeliveryAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            [
                'user_id' => 1, 
                'postal_code' => '111-1111', 
                'address' => 'address1',
                'building' => 'building1',
            ],
            [
                'user_id' => 2,
                'postal_code' => '222-2222',
                'address' => 'address2',
                'building' => 'building2',
            ],
            [
                'user_id' => 3,
                'postal_code' => '333-3333',
                'address' => 'address3',
                'building' => 'building3',
            ],
        ];

        foreach ($params as $param) {
            DeliveryAddress::create($param);
        }
    }
}
