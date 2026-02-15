@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css')}}">
@endsection

@section('content')
<div class="mypage-container">

    {{-- ユーザー情報 --}}
    <div class="profile-area">
        <div class="profile-left">
            <div class="profile-image">
                @if ($user->image)
                    <img src="{{ asset('storage/' . $user->image) }}">
                @else
                    <div class="no-image"></div>
                @endif
            </div>

            <h2 class="user-name">{{ $user->name }}</h2>
        </div>

        <a href="{{ route('profile.edit') }}" class="edit-btn">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ --}}
    <div class="mypage-tabs">
        <a href="{{ url('/mypage?tab=sell') }}"
        class=" tab {{ $tab === 'sell' ? 'active' : '' }}">
            出品した商品
        </a>

        <a href="{{ url('/mypage?tab=buy') }}"
        class="tab {{ $tab === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    {{-- 商品一覧 --}}
    <div class="item-list">
        @php
            $items = $tab === 'buy' ? $buyItems : $sellItems;
        @endphp

        @forelse ($items as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item->id) }}">
                    <div class="item-image">
                        @if ($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}">
                        @else
                            <div class="no-image">商品画像</div>
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
