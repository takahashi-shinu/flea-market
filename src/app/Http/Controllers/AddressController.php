<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 送付先住所変更画面
    public function edit(Item $item)
    {
        $user = Auth::user();

        return view('items.newaddress', [
            'item' => $item,
            'postal_code' => session('shipping_postal_code', $user->postal_code),
            'address' => session('shipping_address', $user->address),
            'building' => session('shipping_building', $user->building),
        ]);
    }

    // 送付先住所更新
    public function update(AddressRequest $request, Item $item)
    {
        // セッションに保存（＝今回の購入用の配送先）
        session([
            'shipping_postal_code' => $request->postal_code,
            'shipping_address'     => $request->address,
            'shipping_building'    => $request->building,
        ]);

        return redirect()
            ->route('purchase.create', $item)
            ->with('success', '配送先を変更しました');
    }
}
