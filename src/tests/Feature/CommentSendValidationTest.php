<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use App\Models\Item;
use App\Models\User;

class CommentSendValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanPostCommentToItem()
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

        //コメント無し、カウント0確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id, 
            'user_id' => $user->id,
        ]);
        $this->assertMatchesRegularExpression(
            '/<span class="comment__count">\s*0\s*<\/span>/',
            $response->getContent()
        );
        $response->assertSee('コメント(0)');

        $response = $this->followingRedirects()->post("/item/{$item->id}/comment", [
            'body' => 'テスト'
        ]);

        //コメント有り、カウント1確認
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id, 
            'user_id' => $user->id, 
            'body' => 'テスト'
        ]);
        $this->assertMatchesRegularExpression(
            '/<span class="comment__count">\s*1\s*<\/span>/',
            $response->getContent()
        );
        $response->assertSee('コメント(1)');
    }

    public function testGuestCannotPostCommentToItem()
    {
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::find(1);

        $response = $this->get("/item/{$item->id}")->assertStatus(200);

        //コメント無し、カウント0確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
        ]);
        $this->assertMatchesRegularExpression(
            '/<span class="comment__count">\s*0\s*<\/span>/',
            $response->getContent()
        );
        $response->assertSee('コメント(0)');

        $this->followingRedirects()->post("/item/{$item->id}/comment", [
            'body' => 'テスト'
        ]);

        //ミドルウェアが作動するので商品一覧ページへ再アクセス
        $response = $this->get("/item/{$item->id}")->assertStatus(200);


        //コメント無し、カウント0確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
        ]);
        $this->assertMatchesRegularExpression(
            '/<span class="comment__count">\s*0\s*<\/span>/',
            $response->getContent()
        );
        $response->assertSee('コメント(0)');
    }

    public function testValidationMessageIsShownWhenCommentBodyIsEmpty()
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

        $this->actingAs($user)->get("/item/{$item->id}")->assertStatus(200);

        $response = $this->post("/item/{$item->id}/comment", [
            'body' => ''
        ]);

        $response->assertSessionHasErrors(['body']);
        $this->followRedirects($response)->assertSee('コメントを入力してください');
    }

    public function testValidationMessageIsShownWhenCommentExceedsMaxLength()
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

        $this->actingAs($user)->get("/item/{$item->id}")->assertStatus(200);

        $longComment = str_repeat('あ', 256);
        $response = $this->post("/item/{$item->id}/comment", [
            'body' => $longComment
        ]);

        $response->assertSessionHasErrors(['body']);
        $this->followRedirects($response)->assertSee('コメントは255文字以下で入力してください');
    }
}
