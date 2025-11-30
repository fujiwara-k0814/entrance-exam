<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;
use App\Models\UserEvaluation;
use App\Models\Message;

class MypageController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        
        return view('profile', compact('user'));
    }
    
    public function store(ProfileRequest $request)
    {
        //画像選択時のみフォーム処理(button nameでのaction判定)
        if (!$request->has('action')) {
            //ブラウザバック時エラー対策
            if (!$request->file('image_path')) {
                return redirect('/mypage/profile')->withInput();
            }

            $imagePath = $request->file('image_path')->store('profile_images', 'public');

            session(['profile_image_path' => "storage/$imagePath"]);

            return redirect('/mypage/profile')->withInput();
        }


        //全体フォーム処理
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $userAddress = $request->only([
            'postal_code',
            'address',
            'building',
        ]);

        if ($user->delivery_address) {
            $user->delivery_address->update($userAddress);
            $redirectPath = '/mypage';
        }else{
            $user->delivery_address()->create($userAddress);
            $redirectPath = '/?tab=mylist';
        }
        $user->image_path = $request->input('image_path');
        $user->name = $request->input('name');
        $user->fill($userAddress);
        $user->save();

        session()->forget('profile_image_path');

        return redirect($redirectPath);
    }

    public function show(Request $request)
    {
        $user = Auth::user();

        if (UserEvaluation::where('targeter_id', $user->id)) {
            $average = round(UserEvaluation::where('targeter_id', $user->id)->avg('score'));
        } else {
            $average = null;
        }

        //取引中アイテムの取得
        //購入済かつ取引未完了
        //その中で自分が購入済かつ取引未完了、または出品済かつ取引未完了
        //該当したアイテムの新規通知件数を取得
        //通知新着順にソート
        $transactionItems = Item::query()
            ->whereNotNull('delivery_address_id')
            ->where(function ($q) {
                $q->whereHas('delivery_address', function ($q2) {
                    $q2->where('user_id', Auth::id());
                })->where('buyer_completed', false)
                ->orWhereHas('sell', function ($q3) {
                    $q3->where('user_id', Auth::id());
                })->where('seller_completed', false);
            })
            ->withCount([
                'messages as unread_messages_count' => function ($q) {
                    $q->where('is_read', false)
                        ->where('sender_id', '!=', Auth::id());
                }
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('item_id', 'items.id')
                    ->where('receiver_id', Auth::id())
                    ->latest()
                    ->take(1)
            )
            ->get();
        //トータル通知件数
        $totalNotifications = $transactionItems->sum('unread_messages_count');

        if ($request->query('page') === "buy") {
            $items = Item::where('delivery_address_id', $user->delivery_address->id)->get();
        }elseif ($request->query('page') === "transaction") {
            $items = $transactionItems;
        }else{
            $items = $user->soldItems;
        }

        //プロフィール画像、出品画像、取引送信画像リセット
        session()->forget([
            'profile_image_path', 'item_image_path', 'add_image_path', 'item_id'
        ]);

        return view('mypage', compact('user', 'items', 'average', 'totalNotifications'));
    }
}
