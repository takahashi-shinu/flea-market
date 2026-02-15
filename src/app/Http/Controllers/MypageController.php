<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // tab（sell / buy） デフォルトは sell
        $tab = $request->query('tab', 'sell');

        // 出品した商品
        $sellItems = $user->items()->latest()->get();

        // 購入した商品
        $buyItems = $user->purchasedItems()
        ->where('purchases.status', \App\Models\Purchase::STATUS_PAID)
        ->latest()
        ->get();

        return view('users.mypage', compact('user', 'sellItems', 'buyItems', 'tab'));
    }
}
