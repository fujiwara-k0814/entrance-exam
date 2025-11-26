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
            <h1 class="mypage-name">{{ $user->name }}</h1>
        </div>
        <a href="/mypage/profile" class="mypage__edit-link">プロフィールを編集</a>
    </div>
    <div class="mypage__select-tab__content">
        <a href="mypage?page=sell" 
            class="{{ request('page') === 'buy' ? 'sold-tab' : 'sold-tab-active' }}">出品した商品</a>
        <a href="mypage?page=buy" 
            class="{{ request('page') === 'buy' ? 'purchased-tab-active' : 'purchased-tab' }}">購入した商品</a>
    </div>
    <div class="item-list__inner">
        @foreach ($items as $item)
                <x-item-card :item="$item" />
        @endforeach
    </div>
</div>
@endsection