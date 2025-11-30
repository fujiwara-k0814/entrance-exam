<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Message;

class UserSendChatTest extends TestCase
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
    //ユーザーチャット送信　バリデーション　本文　必須
    public function test_user_send_chat_validate_content_required()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post("/message/$item->id", [
            'content' => '',
            'image_path' => 'storage/item_images/test_image.jpg',
            'send' => '1',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('content');
        $errors = session('errors');
        $this->assertEquals('本文を入力してください', $errors->first('content'));
    }

    //ユーザーチャット送信　バリデーション　本文　最大文字数
    public function test_user_send_chat_validate_content_max()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post("/message/$item->id", [
            'content' => str_repeat('あ', 401),
            'image_path' => 'storage/item_images/test_image.jpg',
            'send' => '1',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('content');
        $errors = session('errors');
        $this->assertEquals('本文は400文字以内で入力してください', $errors->first('content'));
    }

    //ユーザーチャット送信　バリデーション　画像　形式
    public function test_user_send_chat_validate_image_type()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post("/message/$item->id", [
            'content' => 'test',
            'image_path' => 'storage/item_images/test_image.gif',
            'send' => '1',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('image_path');
        $errors = session('errors');
        $this->assertEquals('「.png」または「.jpeg」形式でアップロードしてください', $errors->first('image_path'));
    }

    //ユーザーチャット送信　送信機能
    public function test_user_send_chat()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $this->actingAs($user)->post("/message/$item->id", [
            'content' => 'test',
            'image_path' => 'storage/item_images/test_image.jpg',
            'send' => '1',
        ]);

        $message = Message::where('sender_id', $user->id)->first();
        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);
        $response->assertViewHas('messages',
            function ($messages) use ($message) {
                return $messages[0]->id === $message['id']
                    && $messages[0]->item_id === $message['item_id']
                    && $messages[0]->sender_id === $message['sender_id']
                    && $messages[0]->receiver_id === $message['receiver_id']
                    && $messages[0]->content === $message['content']
                    && $messages[0]->image_path === $message['image_path'];
            }
        );
    }
}
