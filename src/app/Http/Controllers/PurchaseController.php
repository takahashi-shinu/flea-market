<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    public function create(Item $item)
    {
        if ($item->isSold()) {
            return redirect()->route('items.show', $item);
        }
        return view('items.purchase', [
            'item' => $item,
            'postal_code' => session('shipping_postal_code', auth()->user()->postal_code),
            'address'     => session('shipping_address', auth()->user()->address),
            'building'    => session('shipping_building', auth()->user()->building),
            'paymentMethod' => session('payment_method', 'card'),
        ]);
    }

    public function stripe(Request $request, Item $item)
    {
        $request->validate([
            'payment_method' => 'required|in:card,convenience',
        ]);

        // ========= DB: 予約(pending)確保 =========
        try {
            DB::beginTransaction();

            $lockedItem = Item::where('id', $item->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedItem->isSold()) {
                throw new \Exception('Sold');
            }

            Purchase::where('item_id', $lockedItem->id)
                ->where('status', Purchase::STATUS_PENDING)
                ->where('expires_at', '<=', now())
                ->update(['status' => Purchase::STATUS_EXPIRED]);

            $existingPending = Purchase::where('item_id', $lockedItem->id)
                ->where('status', Purchase::STATUS_PENDING)
                ->where('expires_at', '>', now())
                ->exists();

            if ($existingPending) {
                throw new \Exception('Already purchasing');
            }

            $purchase = Purchase::create([
                'user_id' => auth()->id(),
                'item_id' => $lockedItem->id,
                'payment_method' => $request->payment_method,
                'postal_code' => session('shipping_postal_code', auth()->user()->postal_code),
                'address' => session('shipping_address', auth()->user()->address),
                'building' => session('shipping_building', auth()->user()->building),
                'status' => Purchase::STATUS_PENDING,
                'expires_at' => now()->addMinutes(30),
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            $msg = $e->getMessage();
            if ($msg === 'Sold') {
                return back()->withErrors('この商品は売り切れです');
            }
            if ($msg === 'Already purchasing') {
                return back()->withErrors('他のユーザーが購入手続き中です(30分後に再試行)');
            }

            return back()->withErrors('購入処理に失敗しました');
        }

        // ========= Stripe: セッション作成 =========
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentMethods = $request->payment_method === 'card' ? ['card'] : ['konbini'];

            $session = StripeSession::create([
                'payment_method_types' => $paymentMethods,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $lockedItem->name],
                        'unit_amount' => $lockedItem->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'expires_at' => now()->addMinutes(30)->timestamp,
                'metadata' => [
                    'purchase_id' => $purchase->id,
                ],
            ]);

            $purchase->update(['stripe_session_id' => $session->id]);

            return redirect($session->url);

        } catch (\Exception $e) {
            // Stripe失敗 → 予約解除
            $purchase->update(['status' => Purchase::STATUS_EXPIRED]);

            return back()->withErrors('決済セッション作成に失敗しました');
        }
    }

    private function clearPurchaseSession()
    {
        session()->forget([
            'shipping_postal_code',
            'shipping_address',
            'shipping_building',
            'payment_method',
            'purchased_item_id',
        ]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        $purchase = Purchase::where('stripe_session_id', $sessionId)->first();

        if (!$purchase || !$purchase->isPaid()) {
            return redirect()->route('items.index')
                ->with('info', '現在お支払いを確認中です。');
        }

        $this->clearPurchaseSession();

        return redirect()->route('items.index')
            ->with('success', '購入が確定しました！');
    }
}
