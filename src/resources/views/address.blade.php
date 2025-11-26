@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address__content">
    <h1 class="address__title">住所の変更</h1>
    <form action="/purchase/address/{{ $item->id }}" method="post" class="address-form">
        @csrf
        <div class="address-form__group">
            <label for="postal_code" class="address-form__label">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="address-form__input" 
                value="{{ old('postal_code', $deliveryAddress->postal_code) }}">
            <div class="address-form__error">
                @error('postal_code')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="address-form__group">
            <label for="address" class="address-form__label">住所</label>
            <input type="text" name="address" id="address" class="address-form__input" 
                value="{{ old('address', $deliveryAddress->address) }}">
            <div class="address-form__error">
                @error('address')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="address-form__group">
            <label for="building" class="address-form__label">建物名</label>
            <input type="text" name="building" id="building" class="address-form__input" 
                value="{{ old('building', $deliveryAddress->building) }}">
            <div class="address-form__error">
                @error('building')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <button type="submit" class="address-form__button">更新する</button>
    </form>
</div>
@endsection