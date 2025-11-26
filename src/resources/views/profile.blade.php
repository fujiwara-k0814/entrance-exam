@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile__content">
    <h1 class="profile__title">プロフィール設定</h1>
    <form action="/mypage/profile" method="post" class="profile-form" enctype="multipart/form-data">
        @csrf
        <div class="profile-image__content">
            <div class="profile-image__inner">
                @if (session('profile_image_path'))
                    <img src="{{ asset(session('profile_image_path')) }}" alt="プロフィール画像" class="profile-image">
                @elseif ($user->image_path)
                    <img src="{{ asset($user->image_path) }}" alt="プロフィール画像" class="profile-image">
                @endif
            </div>
            <div class="profile-select__content">
                <label for="image_path" class="profile-select__label">画像を選択する</label>
                <input type="file" name="image_path" id="image_path" class="profile-select__image" 
                    onchange="this.form.submit()">
                <div class="profile-form__error">
                    @error('image_path')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="profile-form__group">
            <label for="name" class="profile-form__label">ユーザー名</label>
            <input type="text" name="name" id="name" class="profile-form__input" value="{{ old('name', $user->name) }}">
            <div class="profile-form__error">
                @error('name')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="profile-form__group">
            <label for="postal_code" class="profile-form__label">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="profile-form__input" 
                value="{{ old('postal_code', $user->postal_code) }}">
            <div class="profile-form__error">
                @error('postal_code')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="profile-form__group">
            <label for="address" class="profile-form__label">住所</label>
            <input type="text" name="address" id="address" class="profile-form__input" 
                value="{{ old('address', $user->address) }}">
            <div class="profile-form__error">
                @error('address')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="profile-form__group">
            <label for="building" class="profile-form__label">建物名</label>
            <input type="text" name="building" id="building" class="profile-form__input" 
                value="{{ old('building', $user->building) }}">
            <div class="profile-form__error">
                @error('building')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <input type="hidden" name="image_path" value="{{ session('profile_image_path') }}">
        <button type="submit" class="profile-form__button" name="action" value="save">更新する</button>
    </form>
</div>
@endsection