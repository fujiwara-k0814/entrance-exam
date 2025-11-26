<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        //メール未認証リダイレクト
        if ($user && !$user->hasVerifiedEmail()) {
            return redirect('/email/verify');
        }

        //プロフィール未登録リダイレクト
        if ($user && !$user->delivery_address) {
            return redirect('/mypage/profile');
        }
        
        if (Auth::check()) {
            if ($request->query('tab') === 'mylist') {
                $items = $user->likedItems()->keywordSearch($request->keyword)->get();
            }else{
                //出品アイテムid取得→除外
                $soldItemIds = $user->soldItems()->pluck('id');
                $items = Item::whereNotIn('id', $soldItemIds)->keywordSearch($request->keyword)->get();
            }
        }else{
            if ($request->query('tab') === 'mylist') {
                $items = collect();
            }else{
                $items = Item::keywordSearch($request->keyword)->get();
            }
        }

        //プロフィール画像、出品画像リセット
        session()->forget(['profile_image_path', 'item_image_path']);

        return view('index', compact('items'));
    }

    public function show($item_id)
    {
        $item = Item::with(['categories', 'comments', 'condition', 'likes'])
                    ->withCount(['comments', 'likes'])
                    ->find($item_id);

        $comments = Comment::where('item_id', $item_id)->get();

        $user = Auth::user();

        //支払い方法リセット
        session()->forget('payment');

        return view('item', compact('item', 'comments', 'user'));
    }
}
