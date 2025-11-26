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

class SearchFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testItemCanBeSearchedByPartialKeyword()
    {
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $response = $this->get('/?keyword=時計');

        $response->assertSee('腕時計');

        //腕時計以外ないことの確認
        $otherNames = Item::where('name', '!=', '腕時計')->pluck('name')->all();
        foreach ($otherNames as $name) {
            $response->assertDontSee($name);
        }
    }


    public function testKeywordSearchIsPreservedInMylist()
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

        //腕時計を含めた商品を3つ取得しマイリストへ登録
        $items = Item::find([1, 2, 3]);
        foreach ($items as $item) {
            Like::create(['user_id' => $user->id, 'item_id' => $item->id]);
        }

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=時計');
        
        $response->assertSee('腕時計');
        $response->assertDontSee('HDD');
        $response->assertDontSee('玉ねぎ3束');
    }
}
