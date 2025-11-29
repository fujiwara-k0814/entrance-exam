<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //出品CO01-CO05
        $param = [
            'name' => 'テストユーザー1',
            'email' => 'user1@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'postal_code' => '111-1111',
            'address' => 'address1',
            'building' => 'building1',
        ];
        User::create($param);

        //出品CO06-CO010
        $param = [
            'name' => 'テストユーザー2',
            'email' => 'user2@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'postal_code' => '222-2222',
            'address' => 'address2',
            'building' => 'building2',
        ];
        User::create($param);

        //未出品
        $param = [
            'name' => 'テストユーザー3',
            'email' => 'user3@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'postal_code' => '333-3333',
            'address' => 'address3',
            'building' => 'building3',
        ];
        User::create($param);
    }
}
