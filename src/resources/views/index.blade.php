@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-card.css') }}">
@endsection

@section('content')
<div class="item-list__content">
    <div class="item-list__select-tab__content">
        <a href="{{ request('keyword') ? '/' . '?' . http_build_query(['keyword' => request('keyword')]) : '/' }}"
            class="{{ request('tab') === 'mylist' ? 'recommend-tab' : 'recommend-tab-active' }}">おすすめ</a>
        <a href="{{ '/' . '?' . http_build_query(array_merge(['tab' => 'mylist'], request()->query())) }}" 
            class="{{ request('tab') === 'mylist' ? 'like-tab-active' : 'like-tab' }}">マイリスト</a>
    </div>
    <div class="item-list__inner">
        @foreach ($items as $item)
                <x-item-card :item="$item" />
        @endforeach
    </div>
</div>
@endsection