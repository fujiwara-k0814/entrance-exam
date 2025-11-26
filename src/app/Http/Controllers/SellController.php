<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::all();

        $conditions = Condition::all();

        return view('sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        //画像選択時のみフォーム処理(button nameでのaction判定)
        if (!$request->has('action')) {
            //ブラウザバック時エラー対策
            if (!$request->file('image_path')) {
                return redirect('/sell')->withInput();
            }

            $imagePath = $request->file('image_path')->store('item_images', 'public');

            session(['item_image_path' => "storage/$imagePath"]);

            return redirect('/sell')->withInput();
        }

        
        //全体フォーム処理
        $itemInformation = $request->only([
            'name',
            'brand',
            'price',
            'description'
        ]);
        $itemInformation['condition_id'] = $request->input('condition');
        $itemInformation['image_path'] = $request->input('image_path');
        $item = Item::create($itemInformation);

        $item->categories()->sync($request->input('category'));

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->sells()->create(['item_id' => $item->id]);

        session()->forget('item_image_path');

        return redirect('/');
    }
}
