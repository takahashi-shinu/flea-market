@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('content')
<div class="top-container">

    {{-- タブ --}}
    <div class="tab-area">
        <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => $keyword]) }}" class="tab {{ $tab === 'recommend' ? 'active' : '' }}">おすすめ</a>

        <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => $keyword]) }}" class="tab {{ $tab === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    {{-- 商品一覧 --}}
    <div class="item-list">
        @php
            $displayItems = $tab === 'mylist' ? $favorites : $items;
        @endphp

        @forelse ($displayItems as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item->id) }}">

                    <div class="item-image">
                        @if ($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}">
                        @else
                            <div class="no-image">商品画像</div>
                        @endif

                        {{-- Sold 表示 --}}
                        @if ($item->is_sold)
                            <span class="sold-badge">Sold</span>
                        @endif
                    </div>

                    <p class="item-name">{{ $item->name }}</p>
                </a>
            </div>
        @empty
            <p class="empty-text">商品がありません</p>
        @endforelse
    </div>

</div>
@endsection