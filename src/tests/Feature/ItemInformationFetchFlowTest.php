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
use App\Models\Comment;

class ItemInformationFetchFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testItemDisplaysAllRequiredFields()
    {
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::find(1);

        User::factory()->create(['name' => 'test1']);
        User::factory()->create(['name' => 'test2']);
        
        $users = User::all();
        foreach ($users as $user) {
            Like::create(['user_id' => $user->id, 'item_id' => $item->id,]);
            
            if ($user->name === 'test1') {
                Comment::create(['user_id' => $user->id, 'item_id' => $item->id, 'body' => 'テスト']);
            }
        }

        $response = $this->get("/item/{$item->id}");
        
        $response->assertSee('良好');
        $response->assertSee('storage/item_images/Armani+Mens+Clock.jpg');
        $response->assertSee('腕時計');
        $response->assertSee('Rolax');
        $response->assertSee('15,000');
        $response->assertSee('スタイリッシュなデザインのメンズ腕時計');
        $this->assertMatchesRegularExpression(
            '/<span class="like__count">\s*2\s*<\/span>/',
            $response->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/<span class="comment__count">\s*1\s*<\/span>/',
            $response->getContent()
        );
        $response->assertSee('ファッション');
        $response->assertSee('メンズ');
        $response->assertSee('アクセサリー');
        $response->assertSee('コメント(1)');
        $response->assertSee('test1');
        $response->assertSee('テスト');
    }

    public function testItemDisplaysMultipleSelectedCategories()
    {
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::find(1);

        $response = $this->get("/item/{$item->id}");

        $response->assertSee('ファッション');
        $response->assertDontSee('家電');
        $response->assertDontSee('インテリア');
        $response->assertDontSee('レディース');
        $response->assertSee('メンズ');
        $response->assertDontSee('コスメ');
        $response->assertDontSee('本');
        $response->assertDontSee('ゲーム');
        $response->assertDontSee('スポーツ');
        $response->assertDontSee('キッチン');
        $response->assertDontSee('ハンドメイド');
        $response->assertSee('アクセサリー');
        $response->assertDontSee('おもちゃ');
        $response->assertDontSee('ベビー・キッズ');
    }
}
