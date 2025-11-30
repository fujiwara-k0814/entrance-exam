<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Item;
use App\Models\User;

class UserEvaluationReviewTest extends TestCase
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
    //ユーザー評価　購入者
    public function test_review_buyer()
    {
        $item = Item::find(6);
        $item->update([
            'delivery_address_id' => 1,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $response = $this->withSession(['showModal' => true])->actingAs($user)->get("/message/$item->id");
        $response->assertSee('class="transaction-modal"', false);

        $response = $this->actingAs($user)
            ->post("/transaction/evaluation/$item->id/2/$user->id", [
                'score' => '5',
        ]);
        $response->assertRedirectContains('/');

        $this->assertDatabaseHas('user_evaluations', [
            'targeter_id' => 2,
            'evaluator_id' => $user->id,
            'score' => 5,
        ]);
    }

    //ユーザー評価　出品者
    public function test_review_seller()
    {
        $item = Item::find(1);
        $item->update([
            'delivery_address_id' => 2,
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)->get("/message/$item->id");
        $response->assertStatus(200);

        $response = $this->withSession(['showModal' => true])->actingAs($user)->get("/message/$item->id");
        $response->assertSee('class="transaction-modal"', false);

        $response = $this->actingAs($user)
            ->post("/transaction/evaluation/$item->id/2/$user->id", [
                'score' => '5',
            ]);
        $response->assertRedirectContains('/');

        $this->assertDatabaseHas('user_evaluations', [
            'targeter_id' => 2,
            'evaluator_id' => $user->id,
            'score' => 5,
        ]);
    }
}
