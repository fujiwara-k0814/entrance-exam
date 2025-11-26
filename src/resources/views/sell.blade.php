@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell__content">
    <h1 class="sell__title">商品の出品</h1>
    <form action="/sell" method="post" class="sell-form" enctype="multipart/form-data">
        @csrf
        <div class="sell-image__content">
            <label for="image" class="sell-image__label">商品画像</label>
            <div class="sell-image__inner">
                @if (session('item_image_path'))
                    <img src="{{ asset(session('item_image_path')) }}" alt="商品画像" class="sell-image">
                @endif
                <div class="sell-select-label__wrapper">
                    <label for="image" class="sell-select__label">
                        <input type="file" name="image_path" id="image" class="sell-image__input" 
                            onchange="this.form.submit()">
                        画像を選択する
                    </label>
                </div>
            </div>
            <div class="sell-form__error">
                @error('image_path')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <h2 class="sell-detail__title">商品の詳細</h2>
        <label class="sell-category__label">カテゴリー</label>
        <div class="sell-category__content">
            @foreach ($categories as $category)
                <input type="checkbox" name="category[]" id="category-{{ $category->id }}" class="category-type" 
                    value="{{ $category->id }}"{{ in_array($category->id, old('category', [])) ? 'checked' : '' }}>
                <label for="category-{{ $category->id }}" class="category-label">{{ $category->name }}</label>
            @endforeach
        </div>
        <div class="sell-form__error">
            @error('category')
                {{ $message }}
            @enderror
        </div>
        <label for="condition" class="sell-condition__label">商品の状態</label>
        <select name="condition" id="condition" class="sell-condition__select">
            <option value="" hidden>選択してください</option>
            @foreach ($conditions as $condition)
                <option class="condition-option" 
                    value="{{ $condition->id }}"{{ old('condition') == $condition->id ? 'selected' : '' }}
                    >{{ $condition->name }}</option>
            @endforeach
        </select>
        <div class="sell-form__error">
            @error('condition')
                {{ $message }}
            @enderror
        </div>
        <h2 class="sell-description__title">商品名と説明</h2>
        <div class="sell-description__group">
            <label for="name" class="sell-group__label">商品名</label>
            <input type="text" name="name" id="name" class="sell-group__input" value="{{ old('name') }}">
            <div class="sell-form__error">
                @error('name')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="sell-description__group">
            <label for="brand" class="sell-group__label">ブランド名</label>
            <input type="text" name="brand" id="brand" class="sell-group__input" value="{{ old('brand') }}">
            <div class="sell-form__error">
                @error('brand')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="sell-description__group">
            <label for="description" class="sell-group__label">商品の説明</label>
            <textarea name="description" id="description" 
                class="sell-group__input sell-group__input--textarea">{{ old('description') }}</textarea>
            <div class="sell-form__error">
                @error('description')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="sell-description__group">
            <label for="price" class="sell-group__label">販売価格</label>
            <div class="sell-group__wrapper">
                <span class="yen-mark">¥</span>
                <input type="text" name="price" id="price" class="sell-group__input sell-group__input--price" 
                    value="{{ old('price') }}">
            </div>
            <div class="sell-form__error">
                @error('price')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <input type="hidden" name="image_path" value="{{ session('item_image_path') }}">
        <button type="submit" class="sell-form__button" name="action" value="save">出品する</button>
    </form>
</div>
@endsection