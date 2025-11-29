<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use App\Http\Requests\MessageRequest;

class MessageController extends Controller
{
    public function show($item_id)
    {
        /** @var \App\Models\User $sender */
        $sender = Auth::user();
        $targetItem = Item::with('sell', 'delivery_address')->find($item_id);
        $seller = User::find($targetItem->sell->user_id);

        if ($sender->id === $seller->id) {
            $receiver = User::find($targetItem->delivery_address->user_id);
            $isSeller = true;
        } else {
            $receiver = $seller;
            $isSeller = false;
        }

        if ($isSeller && $targetItem->buyer_completed) {
            $showModal = true;
        } else {
            $showModal = false;
        }

        $review = $sender->evaluationsGiven()->where('targeter_id', $receiver->id)->first();

        //取引中アイテムの取得
        //購入済かつ取引未完了
        //その中で自分が購入済かつ取引未完了、または出品済かつ取引未完了
        //通知新着順にソート
        $items = Item::query()
            ->whereNotNull('delivery_address_id')
            ->where(function ($q) {
                $q->whereHas('delivery_address', function ($q2) {
                    $q2->where('user_id', Auth::id());
                })->where('buyer_completed', false)
                ->orWhereHas('sell', function ($q3) {
                    $q3->where('user_id', Auth::id());
                })->where('seller_completed', false);
            })
            ->where('id', '!=', $targetItem->id)
            ->with(['messages' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->get();

        $messages = Message::where('item_id', $targetItem->id)->orderBy('created_at')->get();
        

        return view('message', compact(
            'sender', 
            'receiver', 
            'targetItem', 
            'review', 
            'items', 
            'messages',
            'isSeller',
            'showModal',
        ));
    }

    public function store(MessageRequest $request, $item_id) 
    {
        //画像選択時のみフォーム処理(button nameでのaction判定)
        if (!$request->has('send')) {
            //ブラウザバック時エラー対策
            if (!$request->file('image_path')) {
                return redirect("/meessage/$item_id")->withInput();
            }

            $imagePath = $request->file('image_path')->store('message_images', 'public');

            session(['add_image_path' => "storage/$imagePath"]);

            return redirect("/meessage/$item_id")->withInput();
        }

        /** @var \App\Models\User $sender */
        $sender = Auth::user();
        $targetItem = Item::with('sell', 'delivery_address')->find($item_id);
        $seller = User::find($targetItem->sell->user_id);

        if ($sender->id === $seller->id) {
            $receiver = User::find($targetItem->delivery_address->user_id);
        } else {
            $receiver = $seller;
        }

        Message::create([
            'item_id' => $targetItem->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => $request->input('content'),
            'image_path' => $request->input('image_path'),
        ]);

        session()->forget('add_image_path');

        return redirect("/meessage/$item_id");
    }

    public function update(MessageRequest $request, $message_id) 
    {
        $message = Message::find($message_id);

        if ($request->has('delete')) {
            $message->delete();
        } else {
            $message->update([
                'content' => $request->input('message'),
            ]);
        }

        return redirect("/meessage/$message->item_id");
    }
}
