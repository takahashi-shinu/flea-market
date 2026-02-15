<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    // 商品一覧
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');

        // おすすめ商品一覧
        $itemsQuery = Item::query()
        ->when(auth()->check(), function ($q) {
            $q->where('user_id', '!=', auth()->id());
        })
        ->withExists(['purchase as is_sold' => function ($query) {
            $query->where('status', \App\Models\Purchase::STATUS_PAID);
        }]);// ← 購入済み判定

        // 商品名検索
        if ($keyword) {
            $itemsQuery->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $itemsQuery->latest()->get();

        // マイリスト
        $favorites = collect();

        if ($tab === 'mylist' && auth()->check()) {
            $favoritesQuery = Item::whereHas('favorites', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->withExists(['purchase as is_sold' => function ($query) {
                $query->where('status', \App\Models\Purchase::STATUS_PAID);
            }]);

            if ($keyword) {
                $favoritesQuery->where('name', 'like', '%' . $keyword . '%');
            }

            $favorites = $favoritesQuery->latest()->get();
        }

        return view('items.index', compact(
            'items',
            'favorites',
            'tab',
            'keyword'
        ));
    }

    // 商品詳細
    public function show(Item $item)
    {
        $item->load([
            'categories',
            'comments.user',
        ]);

        $likeCount = $item->favorites()->count();
        $commentCount = $item->comments()->count();

        $isLiked = false;
        if (Auth::check() && Auth::id() !== $item->user_id) {
            $isLiked = $item->favorites()
                ->where('user_id', Auth::id())
                ->exists();
        }

        return view('items.item', compact(
            'item',
            'likeCount',
            'commentCount',
            'isLiked'
        ));
    }
}
