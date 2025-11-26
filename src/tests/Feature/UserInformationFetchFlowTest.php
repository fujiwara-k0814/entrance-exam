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
use App\Models\Sell;
use Illuminate\Support\Facades\Auth;

class UserInformationFetchFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserInformationIsDisplayedWithProfileAndItemLists()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'image_path' => 'storage/profile_images/user_image.png',
            'name' => 'test',
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'address' => 'address',
        ]);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $soldItem = Item::find(1);

        $purchaseItem = Item::find(2);

        //出品商品一覧初期状態確認
        $response = $this->actingAs($user)->get('/mypage?page=sell')->assertStatus(200);
        $response->assertSee($user->image_path);
        $response->assertSee($user->name);
        $response->assertDontSee($soldItem->name);

        //購入商品一覧初期状態確認
        $response = $this->actingAs($user)->get('/mypage?page=buy')->assertStatus(200);
        $response->assertSee($user->image_path);
        $response->assertSee($user->name);
        $response->assertDontSee($purchaseItem->name);

        //出品登録
        Sell::create(['user_id' => $user->id, 'item_id' => $soldItem->id,]);

        //購入登録
        $deliveryAddress = DeliveryAddress::where('user_id', $user->id)->get()->first();
        $purchaseItem->update(['delivery_address_id' => $deliveryAddress->id]);

        //user情報再取得
        $user = User::find(Auth::id());

        //出品商品一覧状態確認
        $response = $this->actingAs($user)->get('/mypage?page=sell')->assertStatus(200);
        $response->assertSee($user->image_path);
        $response->assertSee($user->name);
        $response->assertSee($soldItem->name);

        //購入商品一覧状態確認
        $response = $this->actingAs($user)->get('/mypage?page=buy')->assertStatus(200);
        $response->assertSee($user->image_path);
        $response->assertSee($user->name);
        $response->assertSee($purchaseItem->name);
    }
}
