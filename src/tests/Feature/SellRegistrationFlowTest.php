<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use App\Models\User;

class SellRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testItemListingFormSavesAllRequiredInformation()
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

        //商品無し確認
        $this->assertDatabaseMissing('items', [
            'condition_id' => 1,
            'image_path' => 'storage/item_images/test_image.jpg',
            'name' => 'test_name',
            'brand' => 'test_brand',
            'price' => '10000',
            'description' => 'test_description',
        ]);
        $this->assertDatabaseMissing('category_item', [
            'item_id' => 1,
            'item_id' => 1,
            'item_id' => 1,
            'category_id' => 1,
            'category_id' => 2,
            'category_id' => 3,
        ]);

        $this->actingAs($user)->get('/sell')->assertStatus(200);

        //actionはpost分岐での処理に必要なため付与
        $response  = $this->post('/sell', [
            'condition' => 1,
            'image_path' => 'storage/item_images/test_image.jpg',
            'name' => 'test_name',
            'brand' => 'test_brand',
            'price' => '10000',
            'description' => 'test_description',
            'category' => [1, 2, 3],
            'action' => 'save',
        ]);

        //商品有り確認
        $this->assertDatabaseHas('items', [
            'condition_id' => 1,
            'image_path' => 'storage/item_images/test_image.jpg',
            'name' => 'test_name',
            'brand' => 'test_brand',
            'price' => '10000',
            'description' => 'test_description',
        ]);
        $this->assertDatabaseHas('category_item', [
            'item_id' => 1,
            'item_id' => 1,
            'item_id' => 1,
            'category_id' => 1,
            'category_id' => 2,
            'category_id' => 3,
        ]);
    }
}
