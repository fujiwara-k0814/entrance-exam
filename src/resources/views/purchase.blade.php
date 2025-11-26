@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase__content">
    <form action="/purchase/{{ $item->id }}" method="post" class="purchase-form">
        @csrf
        <div class="purchase-item__content">
            <div class="item__content">
                <div class="item-image__inner">
                    <img src="{{ asset($item->image_path) }}" alt="商品画像" class="item-image">
                </div>
                <div class="item__heading">
                    <h1 class="item-name">{{ $item->name }}</h1>
                    <div class="item-price__content">
                        <span class="yen-mark">¥</span>
                        <span class="item-price">{{ number_format($item->price) }}</span>
                    </div>
                </div>
            </div>
            <div class="item-payment__content">
                <label for="payment" class="item-payment__label">支払い方法</label>
                <select name="payment" id="payment" class="item-payment__select" onchange="this.form.submit()">
                    <option value="" hidden>選択してください</option>
                    <option class="payment-option" 
                        value="konbini"{{ session('payment') == 'konbini' ? 'selected' : '' }}>コンビニ払い</option>
                    <option class="payment-option" 
                        value="card"{{ session('payment') == 'card' ? 'selected' : '' }}>カード支払い</option>
                </select>
                <div class="purchase-form__error">
                    @error('payment')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="item-delivery__content">
                <div class="item-delivery__label-wrapper">
                    <span class="item-delivery__label">配送先</span>
                    <a href="/purchase/address/{{ $item->id }}" class="delivery-address__link">変更する</a>
                </div>
                <div class="postal-code__content">
                    <span class="postal-mark">〒</span>
                    <span class="postal-code">{{ $deliveryAddress->postal_code }}</span>
                </div>
                <div class="address__content">
                    <span class="delivery-address">{{ $deliveryAddress->address }}</span>
                    <span class="delivery-building">{{ $deliveryAddress->building }}</span>
                </div>
                <div class="purchase-form__error">
                    @error('delivery_address_id')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="purchase-payment__content">
            <table class="payment-table">
                <tr class="table-row">
                    <th class="table-header">商品代金</th>
                    <td class="table-data-price">
                        <span class="table-yen-mark">¥</span>{{ number_format($item->price) }}
                    </td>
                </tr>
                <tr class="table-row">
                    <th class="table-header">支払い方法</th>
                    @php
                        $paymentLabel = ['konbini' => 'コンビニ払い', 'card' => 'カード支払い'];
                    @endphp
                    <td class="table-data-payment">{{ $paymentLabel[session('payment')] ?? '' }}</td>
                </tr>
            </table>
            <input type="hidden" name="delivery_address_id" value="{{ $deliveryAddress->id }}">
            <button type="submit" class="purchase-form__button" name="action" value="save">購入する</button>
        </div>
    </form>
</div>
@endsection