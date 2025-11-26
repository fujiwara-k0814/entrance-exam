@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item-detail__content">
    <div class="item-image__content">
        @if ($item->delivery_address)
            <div class="sold-label__wrapper">
                <div class="sold-label__content">
                    <span class="sold-label">Sold</span>
                </div>
                <img src="{{ asset($item->image_path) }}" alt="商品画像" class="item-image">
            </div>
        @else
            <img src="{{ asset($item->image_path) }}" alt="商品画像" class="item-image">
        @endif
    </div>
    <div class="item-information__content">
        <h1 class="item-name">{{ $item->name }}</h1>
        <div class="item-brand__content">
            <span class="item-brand">{{ $item->brand }}</span>
        </div>
        <div class="item-price__content">
            <span class="yen-mark">¥</span>
            <span class="item-price">{{ number_format($item->price) }}</span>
            <span class="tax-label">(税込)</span>
        </div>
        <div class="item-icon__content">
            <div class="item-icon__group">
                <div class="item-icon__inner">
                    <form action="/item/{{ $item->id }}/like" method="post" class="item-like-form">
                        @csrf
                        @if (Auth::check())
                            @if ($user->likes()->where('item_id', $item->id)->exists())
                                <button type="submit" class="item-like-button-active">
                                    <img src="/images/liked_icon.svg" alt="いいね画像" class="item__icon">
                                </button>
                            @else
                                <button type="submit" class="item-like-button-active">
                                    <img src="/images/like_icon.svg" alt="いいね画像" class="item__icon">
                                </button>
                            @endif
                        @else
                            <button type="button" class="item-like-button">
                                <img src="/images/like_icon.svg" alt="いいね画像" class="item__icon">
                            </button>
                        @endif
                    </form>
                    <span class="like__count">{{ $item->likes_count }}</span>
                </div>
            </div>
            <div class="item-icon__group">
                <div class="item-icon__inner">
                    <img src="/images/speech_balloon_icon.svg" alt="コメント画像" class="item__icon">
                    <span class="comment__count">{{ $item->comments_count }}</span>
                </div>
            </div>
        </div>
        @if (Auth::check())
            @if (!$item->delivery_address_id)
                @if ($user->sells->where('item_id', $item->id)->isEmpty())
                    <a href="/purchase/{{ $item->id }}" class="purchase-link-active">購入手続きへ</a>
                @else
                    <span class="purchase-link">購入手続きへ</span>
                @endif
            @else
                <span class="purchase-link">購入手続きへ</span>
            @endif
        @else
            <span class="purchase-link">購入手続きへ</span>
        @endif
        <h2 class="item-description__title">商品説明</h2>
        <span class="item-description">{{ $item->description }}</span>
        <h2 class="item-information__title">商品の情報</h2>
        <div class="item-category__content">
            <span class="item-category__label">カテゴリー</span>
            <div class="item-category__group">
                @foreach ($item->categories as $category)
                    <span class="item-category">{{ $category->name }}</span>
                @endforeach
            </div>
        </div>
        <div class="item-condition__content">
            <span class="item-condition__label">商品の状態</span>
            <span class="item-condition">{{ $item->condition->name }}</span>
        </div>
        <div class="user-comment__content">
            <h2 class="user-comment__title">コメント({{$item->comments_count}})</h2>
            @if ($item->comments_count > 0)
                <div class="user-comment__scroll-wrapper">
                    @foreach ($comments as $comment)
                        <div class="user-comment__group">
                            <div class="user-comment__wrapper">
                                <div class="user-image__inner">
                                    @if ($comment->user->image_path)
                                        <img src="{{ asset($comment->user->image_path) }}" alt="画像" class="user-image">
                                    @endif
                                </div>
                                <span class="user-name">{{ $comment->user->name }}</span>
                            </div>
                            <div class="user-comment__inner">
                                <span class="user-comment">{{ $comment->body }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="comment-form__content">
            <label for="comment" class="comment-form__label">商品へのコメント</label>
            <form action="/item/{{ $item->id }}/comment" method="post" class="comment-form">
                @csrf
                <textarea name="body" id="comment" class="comment-body">{{ old('comment') }}</textarea>
                <div class="comment-form__error">
                    @error('body')
                        {{ $message }}
                    @enderror
                </div>
                @if (Auth::user())
                    <button type="submit" class="comment-form__button-active">コメントを送信する</button>
                @else
                    <button type="button" class="comment-form__button">コメントを送信する</button>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection