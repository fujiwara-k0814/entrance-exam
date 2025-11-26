<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Sell;
use App\Models\DeliveryAddress;

class IndexFetchFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAllItemsAreDisplayedOnIndex()
    {
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $this->get('/')
            ->assertStatus(200)
            ->assertSeeTextInOrder(
                Item::orderBy('id')
                    ->pluck('name')
                    ->toArray()
            );
    }


    public function testPurchasedItemsAreDisplayedSold()
    {
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        //Soldラベル無し確認
        $this->get('/')->assertStatus(200)->assertDontSee('Sold');

        //factoryでDeliveryAddressも生成
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => 'address',
        ]);
        $deliveryAddress = DeliveryAddress::where('user_id', $user->id)->get()->first();

        Item::find(1)->update(['delivery_address_id' => $deliveryAddress->id]);

        //Soldラベル有り確認
        $this->get('/')->assertStatus(200)->assertSee('Sold');
    }


    public function testUserSoldItemsAreNotDisplayed()
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

        //出品前商品有り確認
        $this->actingAs($user)->get('/')->assertSee($item->name);

        Sell::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        //出品後商品無し確認
        $this->actingAs($user)->get('/')->assertDontSee($item->name);
    }
}
