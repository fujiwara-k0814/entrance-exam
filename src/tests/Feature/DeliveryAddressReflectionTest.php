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

class DeliveryAddressReflectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSelectedDeliveryAddressIsReflectedOnPurchasePage()
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

        $response = $this->actingAs($user)->get("/purchase/{$item->id}")->assertStatus(200);

        //初期状態確認
        $response->assertSee('123-4567');
        $response->assertSee('address');

        $this->actingAs($user)->get("/purchase/address/{$item->id}")->assertStatus(200);

        $this->post("/purchase/address/{$item->id}", [
            'postal_code' => '765-4321',
            'address' => 'address_test',
        ]);

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        //初期状態確認
        $response->assertSee('765-4321');
        $response->assertSee('address_test');
    }


    public function testDeliveryAddressIsPersistedWithPurchasedItem()
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

        $this->actingAs($user)->get("/purchase/address/{$item->id}")->assertStatus(200);

        $this->post("/purchase/address/{$item->id}", [
            'postal_code' => '765-4321',
            'address' => 'address_test',
        ]);

        $deliveryAddress = DeliveryAddress::where('user_id', $user->id)->get()->first();

        //購入前配送先紐づけ無し確認
        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
            'delivery_address_id' => $deliveryAddress->id,
        ]);

        $this->actingAs($user)->get("/purchase/{$item->id}");

        //actionはpost分岐での処理に必要なため付与
        $this->post("/purchase/{$item->id}", [
            'payment' => 'card',
            'delivery_address_id' => $deliveryAddress->id,
            'action' => 'save',
        ]);

        //購入後配送先紐づけ確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'delivery_address_id' => $deliveryAddress->id,
        ]);
    }
}
