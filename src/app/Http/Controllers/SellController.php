<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    // 出品画面表示
    public function create()
    {
        // カテゴリー一覧取得
        $categories = Category::all();

        return view('items.sell', compact('categories'));
    }

    // 出品処理
    public function store(ExhibitionRequest $request)
    {
        // バリデーション済みデータ取得
        $data = $request->validated();

        // 画像アップロード
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        // 出品者IDを追加
        $data['user_id'] = Auth::id();

        // カテゴリーIDを一旦取り出す
        $categoryIds = $data['category_ids'];
        unset($data['category_ids']);

        // 商品登録
        $item = Item::create($data);

        // カテゴリー紐付け
        $item->categories()->sync($categoryIds);

        return redirect()->route('items.index')
            ->with('success', '商品を出品しました');
    }
}
