@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login__content">
    <h1 class="login__title">ログイン</h1>
    <form action="/login" method="post" class="login-form">
        @csrf
        <div class="login-form__group">
            <label for="email" class="login-form__label">メールアドレス</label>
            <input type="text" name="email" id="email" class="login-form__input" value="{{ old('email') }}">
            <div class="login-form__error">
                @error('email')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="login-form__group">
            <label for="password" class="login-form__label">パスワード</label>
            <input type="password" name="password" id="password" class="login-form__input">
            <div class="login-form__error">
                @error('password')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <button type="submit" class="login-form__button">ログインする</button>
    </form>
    <div class="register-link__button-content">
        <a href="/register" class="register-link__button">会員登録はこちら</a>
    </div>
</div>
@endsection