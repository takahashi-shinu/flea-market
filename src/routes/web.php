<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\StripeWebhookController;

// 未ログインOK
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// メール認証誘導画面
Route::get('/authenticate', function () {
    return view('auth.authenticate');
})->middleware('auth')->name('authenticate');

// 認証＋メール認証済のみ
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/users/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/users/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// コメント機能(未ログインの場合は入力可能、送信不可)
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

// 認証＋メール認証＋初回登録後プロフィール登録済
Route::middleware(['auth', 'verified','profile.completed'])->group(function () {
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');

    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    Route::post('/item/{item}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{item}/stripe', [PurchaseController::class, 'stripe'])->name('purchase.stripe');

    Route::get('/purchase/address/{item}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [AddressController::class, 'update'])->name('purchase.address.update');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');
