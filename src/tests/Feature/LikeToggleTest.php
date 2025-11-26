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

class LikeToggleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testClickingLikeIconAddsItemToLikedList()
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

        $response = $this->actingAs($user)->get("/item/{$item->id}")->assertStatus(200);

        //いいね無し、カウント0確認
        $this->assertDatabaseMissing('likes', ['item_id' => $item->id,'user_id' => $user->id,]);
        $this->assertMatchesRegularExpression(
            '/<span class="like__count">\s*0\s*<\/span>/',
            $response->getContent()
        );

        $response = $this->followingRedirects()->post("/item/{$item->id}/like");

        //いいね有り、カウント1確認
        $this->assertDatabaseHas('likes', ['item_id' => $item->id,'user_id' => $user->id,]);
        $this->assertMatchesRegularExpression(
            '/<span class="like__count">\s*1\s*<\/span>/',
            $response->getContent()
        );
    }


    public function testClickingLikeIconChangesColor()
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

        $response = $this->actingAs($user)->get("/item/{$item->id}")->assertStatus(200);

        //いいね無し、未いいねアイコン確認
        $this->assertDatabaseMissing('likes', ['item_id' => $item->id,'user_id' => $user->id,]);
        $response->assertSee('/images/like_icon.svg');

        $response = $this->followingRedirects()->post("/item/{$item->id}/like");

        //いいね有り、いいね済アイコン確認
        $this->assertDatabaseHas('likes', ['item_id' => $item->id,'user_id' => $user->id,]);
        $response->assertSee('/images/liked_icon.svg');
    }


    public function testClickingLikedIconRemovesLike()
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

        Like::create(['item_id' => $item->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/item/{$item->id}")->assertStatus(200);

        //いいね有り、カウント1確認
        $this->assertDatabaseHas('likes', ['item_id' => $item->id, 'user_id' => $user->id,]);
        $this->assertMatchesRegularExpression(
            '/<span class="like__count">\s*1\s*<\/span>/',
            $response->getContent()
        );

        $response = $this->followingRedirects()->post("/item/{$item->id}/like");

        //いいね無し、カウント0確認
        $this->assertDatabaseMissing('likes', ['item_id' => $item->id, 'user_id' => $user->id,]);
        $this->assertMatchesRegularExpression(
            '/<span class="like__count">\s*0\s*<\/span>/',
            $response->getContent()
        );
    }
}
