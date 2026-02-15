@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <!-- 左カラム -->
    <div class="purchase-left">
        <!-- 商品情報 -->
        <div class="item-box">
            <img src="{{ asset('storage/' . $item->image) }}" class="item-image">
            <div class="item-text">
                <h2>{{ $item->name }}</h2>
                <p class="price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>
        <hr>
        <!-- 支払い方法 -->
        @if (!$item->isSold())
        <form method="POST" action="{{ route('purchase.stripe', $item) }}">
            @csrf
            <div class="section">
                <h3 class="section-title">支払い方法</h3>
                <select name="payment_method" class="payment-select" id="payment-method-select">
                    <option value="">選択してください</option>
                    <option value="convenience"
                        {{ old('payment_method', $paymentMethod) === 'convenience' ? 'selected' : '' }}>
                        コンビニ支払い
                    </option>
                    <option value="card"
                        {{ old('payment_method', $paymentMethod) === 'card' ? 'selected' : '' }}>
                        カード支払い
                    </option>
                </select>
                    @error('payment_method')
                        <p class="error">{{ $message }}</p>
                    @enderror
            </div>
            <hr>
            <!-- 配送先 -->
            <div class="section">
                <div class="address-header">
                    <h3>配送先</h3>
                    <a href="{{ route('purchase.address.edit', $item) }}" class="change-link">変更する</a>
                </div>
                <p class="address-detail">
                    〒{{ $postal_code }}<br>
                    {{ $address }}<br>
                    {{ $building }}
                </p>
            </div>
            <hr>
    </div>
            <!-- 右カラム -->
            <div class="purchase-right">
                <div class="summary-box">
                    <div class="row">
                        <span>商品代金</span>
                        <span>¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="row">
                        <span>支払い方法</span>
                        <span id="payment-method-label">
                            {{ $paymentMethod === 'convenience' ? 'コンビニ支払い' : 'カード支払い' }}
                        </span>
                    </div>
                </div>
                <button type="submit" class="purchase-btn">購入する</button>
            </div>
        </form>
        @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // セレクトボックス
    const select = document.getElementById('payment-method-select');

    // 右側の表示エリア
    const label = document.getElementById('payment-method-label');

    // 支払い方法ラベル定義
    const paymentLabels = {
        convenience: 'コンビニ支払い',
        card: 'カード支払い',
        // '後払い' ← 追加したい場合
    };

    // 変更時イベント
    select.addEventListener('change', () => {
        const selectedValue = select.value;
        label.textContent = paymentLabels[selectedValue];
    });

});
</script>

@endsection
