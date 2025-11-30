@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/message.css') }}">
@endsection

@section('content')
<div class="message__content">
    <div class="other__content">
        <p class="other__title">その他の取引</p>
        @foreach ($items as $item)
            <a href="/message/{{ $item->id }}" class="other-item">
                {{ $item->name }}
            </a>
        @endforeach
    </div>
    <div class="main__content">
        <div class="receiver__content">
            <div class="receiver__wrapper">
                <div class="receiver-image__inner">
                    @if ($receiver->image_path)
                        <img src="{{ asset($receiver->image_path) }}" 
                            alt="プロフィール画像" class="receiver-image">
                    @endif
                </div>
                <p class="receiver__title">{{ $receiver->name }}さんとの取引画面</p>
            </div>
            <input type="checkbox" id="modal" class="modal-display" hidden @if($showModal) checked @endif>
            <label for="modal" class="modal__button" @if($isSeller) hidden @endif>取引を完了する</label>
            <div class="transaction-modal">
                <form 
                    action="/transaction/evaluation/{{ $targetItem->id }}/{{ $receiver->id }}/{{ $sender->id }}" 
                    method="post" class="transaction-modal-form">
                    @csrf
                    <p class="transaction-message">取引が完了しました。</p>
                    <div class="review__content">
                        <span class="review-comment">今回の取引相手はどうでしたか？</span>
                        <div class="review__score">
                            <input type="radio" id="star5" name="score" value="5" 
                                @if($review?->score == 5) checked @endif>
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="score" value="4" 
                                @if($review?->score == 4) checked @endif>
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="score" value="3" 
                                @if($review?->score == 3) checked @endif>
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="score" value="2" 
                                @if($review?->score == 2) checked @endif>
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="score" value="1" 
                                @if($review?->score == 1) checked @endif>
                            <label for="star1">★</label>
                        </div>
                    </div>
                    <div class="button__wrapper">
                        <button type="submit" class="review__button">送信する</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="item__content">
            <div class="item__inner">
                <img src="{{ asset($targetItem->image_path) }}" alt="商品画像" class="item-image">
            </div>
            <div class="item__wrapper">
                <h1 class="item-name">{{ $targetItem->name }}</h1>
                <p class="item-price">￥{{ number_format($targetItem->price) }}</p>
            </div>
        </div>
        <div class="chat__content">
            @foreach ($messages as $message)
                @if ($message->sender_id === $sender->id)
                    <form action="/message/edit/{{ $message->id }}" method="post" class="message__group message__group-sender"  enctype="multipart/form-data">
                        @csrf
                        <div class="user__wrapper user__wrapper-sender">
                            <span class="user-name user-name-sender">{{ $sender->name }}</span>
                            <div class="user-image__inner">
                                @if ($sender->image_path)
                                    <img src="{{ asset($sender->image_path) }}" alt="画像" class="user-image">
                                @endif
                            </div>
                        </div>
                        @if ($message->image_path)
                            <div class="message-image__inner message-image__inner-sender">
                                <img src="{{ asset($message->image_path) }}" alt="画像" class="message-image">
                            </div>
                        @endif
                        <div class="message__inner message__inner-sender">
                            <textarea oninput="autoResize(this)" rows="1" name="message[{{ $message->id }}]" 
                                id="message_{{ $message->id }}" 
                                class="user-message user-message-sender">{{ old("message.$message->id") 
                                ?? $message->content }}</textarea>
                        </div>
                        <div class="button__wrapper">
                            <button type="submit" class="message__button" name="edit" value="1">編集</button>
                            <button type="submit" class="message__button" name="delete" value="1">削除</button>
                        </div>
                        <div class="message__error">
                            @error("message.$message->id")
                                {{ $message }}
                            @enderror
                        </div>
                    </form>
                @else
                    <div class="message__group">
                        <div class="user__wrapper">
                            <div class="user-image__inner">
                                @if ($receiver->image_path)
                                    <img src="{{ asset($receiver->image_path) }}" alt="画像" class="user-image">
                                @endif
                            </div>
                            <span class="user-name">{{ $receiver->name }}</span>
                        </div>
                        @if ($message->image_path)
                            <div class="message-image__inner">
                                <img src="{{ asset($message->image_path) }}" alt="画像" class="message-image">
                            </div>
                        @endif
                        <div class="message__inner">
                            <span class="user-message">{!! nl2br(e($message->content)) !!}</span>
                        </div>
                    </div>
                @endif
            @endforeach
            <form action="/message/{{ $targetItem->id }}" method="post" class="send-form" enctype="multipart/form-data" id="sendForm">
                @csrf
                <div class="send-image__inner">
                    @if (session('add_image_path'))
                        <img src="{{ asset(session('add_image_path')) }}" alt="送信画像" class="send-image">
                    @endif
                </div>
                <div class="send-form__error">
                    @error('content')
                        {{ $message }}
                    @enderror
                    @error('image_path')
                        {{ $message }}
                    @enderror
                </div>
                <div class="send__content">
                    <textarea name="content" id="content" class="send__input" 
                        placeholder="取引メッセージを記入してください" 
                        oninput="autoResize(this)">{{ old('content') }}</textarea>
                    <div class="send-button__wrapper">
                        <input type="file" name="image_path" 
                            id="image_path" hidden onchange="this.form.submit()">
                        <label for="image_path" class="image-add__label">画像を追加</label>
                        <input type="hidden" name="image_path" value="{{ session('add_image_path') }}">
                        <button type="submit" class="send__button" name="send" value="1">
                            <img src="/images/send_icon.jpg" alt="送信画像">
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@if(session('success'))
<script>
    //メッセージ送信成功時削除
    const messageId = {{ $targetItem->id }};
    localStorage.removeItem('draft_content_' + messageId);
</script>
@endif
<script>
    const chatId = {{ $targetItem->id }};
    const inputarea = document.getElementById('content');

    //画面ロード時に各チャット毎に'content'を復元
    inputarea.value = localStorage.getItem('draft_content_' + chatId) || "";

    //各チャット毎に入力毎時'content'をブラウザの'localStorage'に保存
    inputarea.addEventListener('input', () => {
        localStorage.setItem('draft_content_' + chatId, inputarea.value);
    });

    //送信欄のtextarea高さ調整
    window.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('textarea').forEach(textarea => {
            autoResize(textarea);
        });
    });

    //高さ調整関数
    function autoResize(el) {
        //高さリセット
        el.style.height = 'auto';
        //値によって可変
        el.style.height = el.scrollHeight + 'px';
    }
    window.autoResize = autoResize;
</script>
@endsection