<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // いいねの追加・解除
    public function toggle(Item $item)
    {
        $user = Auth::user();

        // 出品者自身はいいね不可
        if ($item->user_id === $user->id) {
            return back()->with('error', '自分の商品にはいいねできません');
        }

        // すでにいいねしているか？
        $isFavorited = $item->favorites()
            ->where('user_id', $user->id)
            ->exists();

        if ($isFavorited) {
            // いいね解除
            $item->favorites()
                ->where('user_id', $user->id)
                ->delete();
        } else {
            // いいね追加
            $item->favorites()
                ->create([
                    'user_id' => $user->id,
                ]);
        }

        return back();
    }
}
