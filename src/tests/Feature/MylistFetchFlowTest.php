<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Like;

class MylistFetchFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserMylistLikedItemsAreDisplayed()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'address' => 'address',
        ]);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::find(1);

        //未いいね商品無し確認
        $this->actingAs($user)->get('/?tab=mylist')->assertDontSee($item->name);

        Like::create(['user_id' => $user->id,'item_id' => $item->id,]);

        //いいね済商品有り確認
        $this->actingAs($user)->get('/?tab=mylist')->assertSee($item->name);
    }


    public function testUserMylistPurchasedItemsAreDisplayedSold()
    {
        /** @var \App\Models\User $user */
        //初期登録処理　DeliveryAddress付与
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'address' => 'address',
        ]);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::find(1);

        Like::create(['user_id' => $user->id, 'item_id' => $item->id,]);

        //Soldラベル無し確認
        $this->actingAs($user)->get('/?tab=mylist')->assertDontSee('Sold');

        $item->update(['delivery_address_id' => 1]);

        //Soldラベル有り確認
        $this->actingAs($user)->get('/?tab=mylist')->assertSee('Sold');
    }


    public function testGuestMylistItemsAreNotDisplayed()
    {
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $itemsName = Item::pluck('name')->all();

        foreach ($itemsName as $itemName) {
            $this->get('/?tab=mylist')->assertDontSee($itemName);
        }
    }
}
