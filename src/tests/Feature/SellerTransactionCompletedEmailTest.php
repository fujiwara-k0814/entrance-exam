<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class SellerTransactionCompletedEmailTest extends TestCase
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
    //出品者　取引完了メール通知
    public function test_seller_transaction_completed_email()
    {
        Mail::fake();

        $item = Item::find(6);
        $item->update([
            'delivery_address_id' => 1,
        ]);

        $user = User::find(1);
        $this->withSession(['showModal' => true])->actingAs($user)->get("/message/$item->id");

        $this->actingAs($user)
            ->post("/transaction/evaluation/$item->id/2/$user->id", [
                'score' => '5',
            ]);

        $seller = User::find(2);
        Mail::assertSent(TransactionCompletedMail::class, 
            function (TransactionCompletedMail $mail) use ($seller) {
            return $mail->hasTo($seller->email);
        });
    }
}
