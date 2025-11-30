<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Message;

class UserChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    //取引チャット　取引中商品
    public function test_chat_check_items_in_transaction()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get('/mypage?page=transaction');
        $response->assertStatus(200);
        $response->assertViewHas('items',
            function ($items) use ($item) {
                return $items[0]->name === $item['name']
                    && $items[0]->price === $item['price']
                    && $items[0]->brand === $item['brand']
                    && $items[0]->description === $item['description']
                    && $items[0]->image_path === $item['image_path']
                    && $items[0]->condition_id === $item['condition_id'];
            }
        );

        $user = User::find(2);
        $response = $this->actingAs($user)->get('/mypage?page=transaction');
        $response->assertStatus(200);
        $response->assertViewHas('items',
            function ($items) use ($item) {
                return $items[0]->name === $item['name']
                    && $items[0]->price === $item['price']
                    && $items[0]->brand === $item['brand']
                    && $items[0]->description === $item['description']
                    && $items[0]->image_path === $item['image_path']
                    && $items[0]->condition_id === $item['condition_id'];
            }
        );
    }
    //取引チャット　総メッセージ件数
    public function test_chat_check_the_number_of_total_cases()
    {
        $item1 = Item::find(1);
        $item1->update([
            'delivery_address_id' => 2,
        ]);
        Message::create([
            'item_id' => $item1->id,
            'sender_id' => 2,
            'receiver_id' => 1,
            'content' => 'test1',
        ]);

        $item2 = Item::find(2);
        $item2->update([
            'delivery_address_id' => 2,
        ]);
        Message::create([
            'item_id' => $item2->id,
            'sender_id' => 2,
            'receiver_id' => 1,
            'content' => 'test2',
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get('/mypage?page=transaction');
        $response->assertStatus(200);
        $response->assertViewHas('totalNotifications', 2);
    }

    //取引チャット　チャット画面遷移
    public function test_chat_screen_transition()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get('/mypage?page=transaction');
        $response->assertStatus(200);
        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);
    }

    //取引チャット　他の取引商品画面遷移
    public function test_chat_other_item_screen_transition()
    {
        $item1 = Item::find(1);
        $item1->update([
            'delivery_address_id' => 2,
        ]);
        $item2 = Item::find(2);
        $item2->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get("/message/$item1->id");
        $response->assertStatus(200);
        $response = $this->actingAs($user)->get("/message/$item2->id");
        $response->assertStatus(200);
    }

    //取引チャット　新着順ソート
    public function test_chat_sort_by_newst()
    {
        $item1 = Item::find(1);
        $item1->update([
            'delivery_address_id' => 2,
        ]);
        $item2 = Item::find(2);
        $item2->update([
            'delivery_address_id' => 2,
        ]);

        Message::create([
            'item_id' => $item1->id,
            'sender_id' => 2,
            'receiver_id' => 1,
            'content' => 'test1',
        ]);

        $this->travel(3)->hours();

        Message::create([
            'item_id' => $item2->id,
            'sender_id' => 2,
            'receiver_id' => 1,
            'content' => 'test2',
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get('/mypage?page=transaction');
        $response->assertStatus(200);
        $response->assertViewHas('items',
            function ($items) use ($item1, $item2) {
                return $items[0]->name === $item2['name']
                    && $items[0]->price === $item2['price']
                    && $items[0]->brand === $item2['brand']
                    && $items[0]->description === $item2['description']
                    && $items[0]->image_path === $item2['image_path']
                    && $items[0]->condition_id === $item2['condition_id']

                    && $items[1]->name === $item1['name']
                    && $items[1]->price === $item1['price']
                    && $items[1]->brand === $item1['brand']
                    && $items[1]->description === $item1['description']
                    && $items[1]->image_path === $item1['image_path']
                    && $items[1]->condition_id === $item1['condition_id'];
            }
        );
    }

    //取引チャット　各通知件数
    public function test_chat_check_the_number_of_separate_cases()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        Message::create([
            'item_id' => $item->id,
            'sender_id' => 2,
            'receiver_id' => 1,
            'content' => 'test1',
        ]);
        Message::create([
            'item_id' => $item->id,
            'sender_id' => 2,
            'receiver_id' => 1,
            'content' => 'test1',
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get('/mypage?page=transaction');
        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) {
            return $items[0]->unread_messages_count === 2;
        });
    }
}
