@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-card.css') }}">
@endsection

@section('content')
<div class="mypage__content">
    <div class="mypage-image__content">
        <div class="mypage-image__wrapper">
            <div class="mypage-image__inner">
                @if ($user->image_path)
                    <img src="{{ asset($user->image_path) }}" alt="プロフィール画像" class="mypage-image">
                @endif
            </div>
            <div class="mypage-review__content">
                <h1 class="mypage-name">{{ $user->name }}</h1>
                <div class="review__wrapper">
                    @if ($average !== null)
                        {{-- '5' -> 評価が5段階あるため --}}
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $average)
                                <span class="review good">★</span>
                            @else
                                <span class="review">★</span>
                            @endif
                        @endfor
                    @endif
                </div>
            </div>
        </div>
        <a href="/mypage/profile" class="mypage__edit-link">プロフィールを編集</a>
    </div>
    <div class="mypage__select-tab__content">
        @if (request('page') === 'sell' || request('page') === null)
            <a href="mypage?page=sell" class="sold-tab-active">出品した商品</a>
        @else
            <a href="mypage?page=sell" class="sold-tab">出品した商品</a>
        @endif
        @if (request('page') === 'buy')
            <a href="mypage?page=buy" class="purchased-tab-active">購入した商品</a>
        @else
            <a href="mypage?page=buy" class="purchased-tab">購入した商品</a>
        @endif
        @if (request('page') === 'transaction')
            <a href="mypage?page=transaction" class="transaction-tab-active">取引中の商品</a>
        @else
            <a href="mypage?page=transaction" class="transaction-tab">取引中の商品</a>
        @endif
        @if ($totalNotifications > 0)
            <span class="total-notifications">{{ $totalNotifications }}</span>
        @endif
    </div>
    <div class="item-list__inner">
        @foreach ($items as $item)
                <x-item-card :item="$item" />
        @endforeach
    </div>
</div>
@endsection