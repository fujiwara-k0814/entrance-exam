<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use App\Models\Item;
use App\Models\User;

class PaymentMethodSelectionFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSelectedPaymentMethodIsReflectedOnPurchaseConfirmationPage()
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

        //支払い方法初期状態確認
        $this->assertMatchesRegularExpression(
            '/<option[^>]*value="konbini"[^>]*>コンビニ払い<\/option>/',
            $response->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/<option[^>]*value="card"[^>]*>カード支払い<\/option>/',
            $response->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/<td class="table-data-payment">\s*<\/td>/',
            $response->getContent()
        );

        $response = $this->followingRedirects()->post("/purchase/{$item->id}", [
            'payment' => 'card',
            'delivery_address_id' => $user->delivery_address->id,
        ]);

        //支払い方法状態確認
        $this->assertMatchesRegularExpression(
            '/<option[^>]*value="konbini"[^>]*>コンビニ払い<\/option>/',
            $response->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/<option[^>]*value="card"[^>]*selected[^>]*>カード支払い<\/option>/',
            $response->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/<td class="table-data-payment">\s*カード支払い\s*<\/td>/',
            $response->getContent()
        );
    }
}
