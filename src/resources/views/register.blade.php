@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register__content">
    <h1 class="register__title">会員登録</h1>
    <form action="/register" method="post" class="register-form">
        @csrf
        <div class="register-form__group">
            <label for="name" class="register-form__label">ユーザー名</label>
            <input type="text" name="name" id="name" class="register-form__input" value="{{ old('name') }}">
            <div class="register-form__error">
                @error('name')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="register-form__group">
            <label for="email" class="register-form__label">メールアドレス</label>
            <input type="text" name="email" id="email" class="register-form__input" value="{{ old('email') }}">
            <div class="register-form__error">
                @error('email')
                    {!! nl2br(e($message)) !!}
                @enderror
            </div>
        </div>
        <div class="register-form__group">
            <label for="password" class="register-form__label">パスワード</label>
            <input type="password" name="password" id="password" class="register-form__input" 
                value="{{ old('password') }}">
            <div class="register-form__error">
                @error('password')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="register-form__group">
            <label for="password_confirmation" class="register-form__label">確認用パスワード</label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                class="register-form__input" value="{{ old('password_confirmation') }}">
            <div class="register-form__error">
                @error('password_confirmation')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <button type="submit" class="register-form__button">登録する</button>
    </form>
    <div class="login-link__button-content">
        <a href="/login" class="login-link__button">ログインはこちら</a>
    </div>
</div>
@endsection