<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\UserEvaluation;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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

        $item = Item::with('sell')->find($item_id);
        if ($item->sell->user_id === Auth::id()) {
            $item->update([
                'seller_completed' => true,
            ]);
        } else {
            $item->update([
                'buyer_completed' => true,
            ]);
            
            $seller = User::find($item->sell->user_id);
            Mail::raw("取引が完了しました。", function ($message) use ($seller) {
                $message->to($seller->email)->subject('取引完了のお知らせ');
            });
        }

        return redirect('/');
    }
}
