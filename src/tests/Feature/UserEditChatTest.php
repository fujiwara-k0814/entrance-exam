<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Message;

class UserEditChatTest extends TestCase
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
    //ユーザーメッセージ　編集
    public function test_user_message_edit()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $message = Message::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => 2,
            'content' => 'test',
            'image_path' => 'storage/item_images/test_image.jpg',
        ]);

        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $this->actingAs($user)->post("/message/edit/$message->id", [
            'edit' => '1',
            'message' => [
                $message->id => 'test_test'
            ],
        ]);

        $this->assertDatabaseHas('messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => 2,
            'content' => 'test_test',
            'image_path' => 'storage/item_images/test_image.jpg',
        ]);
    }

    //ユーザーメッセージ　編集　バリデーション　本文　必須
    public function test_user_message_edit_validate_content_required()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $message = Message::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => 2,
            'content' => 'test',
            'image_path' => 'storage/item_images/test_image.jpg',
        ]);

        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post("/message/edit/$message->id", [
            'edit' => '1',
            'message' => [
                $message->id => ''
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors("message.$message->id");
        $errors = session('errors');
        $this->assertEquals('本文を入力してください', $errors->first("message.$message->id"));
    }

    //ユーザーメッセージ　編集　バリデーション　本文　最大文字数
    public function test_user_message_edit_validate_content_max()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $message = Message::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => 2,
            'content' => 'test',
            'image_path' => 'storage/item_images/test_image.jpg',
        ]);

        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post("/message/edit/$message->id", [
            'edit' => '1',
            'message' => [
                $message->id => str_repeat('あ', 401)
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors("message.$message->id");
        $errors = session('errors');
        $this->assertEquals('本文は400文字以内で入力してください', $errors->first("message.$message->id"));
    }

    //ユーザーメッセージ　削除
    public function test_user_message_delete()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $message = Message::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => 2,
            'content' => 'test',
            'image_path' => 'storage/item_images/test_image.jpg',
        ]);

        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $this->actingAs($user)->post("/message/edit/$message->id", [
            'delete' => '1',
        ]);

        $this->assertDatabaseMissing('messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => 2,
            'content' => 'test',
            'image_path' => 'storage/item_images/test_image.jpg',
        ]);
    }
}
