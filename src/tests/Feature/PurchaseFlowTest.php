<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use App\Models\Item;
use App\Models\User;
use App\Models\DeliveryAddress;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanPurchaseItemFromDetailPage()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'address' => 'address',
        ]);

        $deliveryAddress = DeliveryAddress::where('user_id', $user->id)->get()->first();

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::find(1);

        $this->actingAs($user)->get("/purchase/{$item->id}")->assertStatus(200);

        //actionはpost分岐での処理に必要なため付与
        $this->post("/purchase/{$item->id}", [
            'payment' => 'card',
            'delivery_address_id' => $deliveryAddress->id,
            'action' => 'save',
        ]);

        //購入完了
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'delivery_address_id' => $deliveryAddress->id,
        ]);
    }


    public function testSoldLabelIsShownForPurchasedItemInItemList()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'address' => 'address',
        ]);

        $deliveryAddress = DeliveryAddress::where('user_id', $user->id)->get()->first();

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        //Soldラベル無し確認
        $this->get('/')->assertStatus(200)->assertDontSee('Sold');

        $item = Item::find(1);

        $this->actingAs($user)->get("/purchase/{$item->id}")->assertStatus(200);

        //actionはpost分岐での処理に必要なため付与
        $this->post("/purchase/{$item->id}", [
            'payment' => 'card',
            'delivery_address_id' => $deliveryAddress->id,
            'action' => 'save',
        ]);

        $this->get('/')->assertStatus(200)->assertSee('Sold');
    }


    public function testPurchasedItemIsListedInProfilePurchaseSection()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'address' => 'address',
        ]);

        $deliveryAddress = DeliveryAddress::where('user_id', $user->id)->get()->first();

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::find(1);

        //購入一覧無し確認
        $this->actingAs($user)->get('/mypage?page=buy')->assertDontSee($item->name);

        $this->actingAs($user)->get("/purchase/{$item->id}")->assertStatus(200);

        //actionはpost分岐での処理に必要なため付与
        $this->post("/purchase/{$item->id}", [
            'payment' => 'card',
            'delivery_address_id' => $deliveryAddress->id,
            'action' => 'save',
        ]);

        $this->actingAs($user)->get('/mypage?page=buy')->assertSee($item->name);
    }
}
