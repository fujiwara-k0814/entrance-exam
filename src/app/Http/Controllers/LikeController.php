<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function update($item_id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        //いいねの有無で生成削除(ルート共通化)
        if ($user->likes()->where('item_id', $item_id)->exists()) {
            $user->likes()->where('item_id', $item_id)->delete();
        }else{
            $user->likes()->create(['item_id' => $item_id]);
        }

        return redirect("/item/$item_id");
    }
}
