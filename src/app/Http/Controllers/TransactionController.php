<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\UserEvaluation;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class TransactionController extends Controller
{
    public function upsert(Request $request, $item_id, $receiver_id, $sender_id)
    {
        $review = UserEvaluation::where([
            'targeter_id' => $receiver_id,
            'evaluator_id' => $sender_id,
        ])
        ->first();

        $score = $request->input('score') ? $request->input('score') : 0;

        //ユーザーレビューレコードの有無で編集か新規作成
        if ($review) {
            $review->update([
                'score' => $score,
            ]);
        } else {
            UserEvaluation::create([
                'targeter_id' => $receiver_id,
                'evaluator_id' => $sender_id,
                'score' => $score,
            ]);
        }

        //レビューをしたのが出品者か判定
        //'items'レコードに出品者か購入者の取引完了フラグを立てる
        $item = Item::with('sell')->find($item_id);
        if ($item->sell->user_id === Auth::id()) {
            $item->update([
                'seller_completed' => true,
            ]);
        } else {
            $item->update([
                'buyer_completed' => true,
            ]);

            //購入者の場合は出品者にメールを送信
            $seller = User::find($item->sell->user_id);
            Mail::to($seller->email)
                ->send(new TransactionCompletedMail($item->name, $seller->email));
        }

        return redirect('/');
    }
}
