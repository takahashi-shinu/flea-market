@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item-detail">
    {{-- 左：商品画像 --}}
    <div class="item-image">
        <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像">
    </div>
    {{-- 右：商品情報 --}}
    <div class="item__info">
        <div class="item__info-inner">
            <h1 class="item-name">{{ $item->name }}</h1>
            <p class="item-brand">{{ $item->brand_name }}</p>
            <p class="item-price">¥{{ number_format($item->price) }} <span>（税込）</span></p>
            {{-- いいね & コメント --}}
            <div class="item-icons">
                {{-- いいね --}}
                @auth
                    @if(auth()->id() !== $item->user_id)
                        <form action="{{ route('favorites.toggle', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="icon-btn">
                                <img
                                    src="{{ asset($isLiked ? 'storage/icons/heart_pink.png' : 'storage/icons/heart_default.png') }}"
                                    alt="いいね"
                                >
                                <span>{{ $likeCount }}</span>
                            </button>
                        </form>
                    @else
                        {{-- 出品者自身：いいね不可（表示のみ） --}}
                        <div class="icon-btn disabled">
                            <img src="{{ asset('storage/icons/heart_default.png') }}" alt="いいね不可">
                            <span>{{ $likeCount }}</span>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="icon-btn">
                        <img src="{{ asset('storage/icons/heart_default.png') }}">
                        <p class="favorite-count">{{ $likeCount }}</p>
                    </a>
                @endauth

                {{-- コメント数 --}}
                <div class="comment__icon">
                    <img src="{{ asset('storage/icons/comment.png') }}" class="comment__icon-image">
                    <p class="comment-count">{{ $commentCount }}</p>
                </div>
            </div>

            {{-- 購入ボタン --}}
            @auth
                @if(auth()->id() !== $item->user_id && !$item->is_sold)
                <a href="{{ route('purchase.create', $item->id) }}" class="buy-btn">購入手続きへ</a>
                @elseif($item->is_sold)
                    <span class="sold-label">SOLD</span>
                @else
                    <span class="seller-label">あなたの出品商品です</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="buy-btn">
                    購入手続きへ
                </a>
            @endauth

            {{-- 商品説明 --}}
            <h2>商品説明</h2>
            <p class="description">{{ $item->description }}</p>

            {{-- 商品情報 --}}
            <h2>商品の情報</h2>

            <div class="info__row">
                <span class="info__row-subject">カテゴリー</span>
                <div class="category-tags">
                    @foreach($item->categories as $category)
                        <span class="tag">{{ $category->name }}</span>
                    @endforeach
                </div>
            </div>

            <div class="info__row">
                <span class="info__row-subject">商品の状態</span>
                <span>{{ $item->condition }}</span>
            </div>

            {{-- コメント --}}
            <h2>コメント（{{ $commentCount }}）</h2>

            <div class="comments">
                @foreach($item->comments as $comment)
                    <div class="comment {{ $comment->user_id === $item->user_id ? 'seller-comment' : '' }}">
                        <div class="comment-user">
                            <div class="comment-avatar">
                                @if ($comment->user->image)
                                    <img src="{{ asset('storage/' . $comment->user->image) }}" alt="ユーザー画像">
                                @else
                                    <div class="user-image-placeholder"></div>
                                @endif
                            </div>
                            <strong>{{ $comment->user->name }}</strong>
                            @if($comment->user_id === $item->user_id)
                                <span class="seller-badge">出品者</span>
                            @endif
                        </div>
                        <p class="comment-text">{{ $comment->comment }}</p>
                    </div>
                @endforeach
            </div>

            {{-- コメント投稿 --}}
            <div>
                <form action="{{ route('comments.store', $item->id) }}" method="POST">
                    @csrf
                    <p class="comment__form-title">商品へのコメント</p>
                    <textarea class="comment__form" name="comment">{{ old('comment') }}</textarea>
                    <p class="comment__error-message">
                    @error('comment')
                    {{ $message }}
                    @enderror
                    </p>

                    @guest
                        <button type="button" class="btn-disabled">
                            コメントを送信する
                        </button>
                    @endguest

                    @auth
                    <button class="comment-btn">コメントを送信する</button>
                    @endauth
                </form>
            </div>
        </div>
    </div>
</div>
@endsection