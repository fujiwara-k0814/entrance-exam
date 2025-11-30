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

        //チャット画面の送信者、受信者振り分け
        //送信者が出品者の場合、UI判別用でフラグを立てる
        if ($sender->id === $seller->id) {
            $receiver = User::find($targetItem->delivery_address->user_id);
            $isSeller = true;
        } else {
            $receiver = $seller;
            $isSeller = false;
        }

        //購入者が取引完了時の出品者モーダル表示としてフラグを立てる
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
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('item_id', 'items.id')
                    ->where('receiver_id', Auth::id())
                    ->latest()
                    ->take(1)
            )
            ->get();

        //チャット画面ロード時に新着メッセージに対して既読のフラグを立てる
        $messages = Message::where('item_id', $targetItem->id)->orderBy('created_at')->get();
        foreach ($messages as $message) {
            if ($message->receiver_id === Auth::id()) {
                $message->update([
                    'is_read' => true,
                ]);
            }
        }

        //送信画像を選択後に他のチャット画面に遷移した際にリセット
        if (session('item_id') !== $item_id) {
            session()->forget('add_image_path');
        }

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
        //画像選択時のみフォーム処理(button nameでのsend判定)
        if (!$request->has('send')) {
            //ブラウザバック時エラー対策
            if (!$request->file('image_path')) {
                return redirect("/message/$item_id")->withInput();
            }

            $imagePath = $request->file('image_path')->store('message_images', 'public');

            //画像の各チャット画面判定用でitem_id付与
            session([
                'add_image_path' => "storage/$imagePath",
                'item_id' => $item_id,
            ]);

            return redirect("/message/$item_id")->withInput();
        }

        /** @var \App\Models\User $sender */
        $sender = Auth::user();
        $targetItem = Item::with('sell', 'delivery_address')->find($item_id);
        $seller = User::find($targetItem->sell->user_id);

        //送信者と受信者の振り分け
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

        session()->forget('add_image_path', 'item_id');

        return redirect("/message/$item_id")->with('success', true);
    }

    public function update(MessageRequest $request, $message_id) 
    {
        $message = Message::find($message_id);

        if ($request->has('delete')) {
            $message->delete();
        } else {
            $message->update([
                'content' => $request->input("message.$message_id"),
            ]);
        }

        return redirect("/message/$message->item_id");
    }
}
