<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;

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

        if ($request->query('page') === "buy") {
            $items = Item::where('delivery_address_id', $user->delivery_address->id)->get();
        }else{
            $items = $user->soldItems;
        }

        //プロフィール画像、出品画像リセット
        session()->forget(['profile_image_path', 'item_image_path']);

        return view('mypage', compact('user', 'items'));
    }
}
