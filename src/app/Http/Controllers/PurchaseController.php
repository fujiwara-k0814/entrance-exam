<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::find($item_id);

        $user = Auth::user();
        $deliveryAddress = $user->delivery_address;

        return view('purchase', compact('item', 'deliveryAddress'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        //支払い方法選択時のみフォーム処理(button nameでのaction判定)
        if (!$request->has('action')) {
            session(['payment' => $request->input('payment')]);

            return redirect("/purchase/$item_id")->withInput();
        }

        
        //全体フォーム処理
        //DB処理
        $item = Item::find($item_id);
        $item->delivery_address_id = $request->input('delivery_address_id');
        $item->save();
        
        //Stripe処理
        Stripe::setApiKey(config('services.stripe.secret')); 
        $session = Session::create([
            'payment_method_types' => [$request->input('payment')],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => ['name' => $item->name],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/'),
            'cancel_url' => url("/purchase/$item_id"),
            'metadata' => [
                'item_id' => $item->id,
                'delivery_address_id' => $request->input('delivery_address_id'),
            ],
        ]);

        session()->forget('payment');

        return redirect($session->url);
    }
}
