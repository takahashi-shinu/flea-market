@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>
    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- 商品画像 --}}
        <div class="form-section">
            <label class="section-label">商品画像</label>
            {{-- id="image-preview" を追加 --}}
            <div class="image-upload" id="image-preview">
                <label class="image-upload__button">
                    画像を選択する
                    {{-- id="item-image" を追加 --}}
                    <input type="file" name="image" id="item-image" style="display:none;" accept="image/*">
                </label>
            </div>
            <p class="sell__form-error-message">
                @error('image')
                {{ $message }}
                @enderror
            </p>
        </div>

        {{-- 商品の詳細 --}}
        <div class="form-section">
            <h2 class="section-title">商品の詳細</h2>
            {{-- カテゴリー --}}
            <div class="form-group">
                <label class="group-label">カテゴリー</label>
                <div class="category-list">
                    @foreach ($categories as $category)
                        <label class="category-item">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="sell__form-error-message">
                @error('category_ids')
                {{ $message }}
                @enderror
                </p>
            </div>

            {{-- 商品の状態 --}}
            <div class="form-group">
                <label class="group-label">商品の状態</label>
                <select name="condition" class="select-box">
                    <option value="">選択してください</option>
                    @foreach(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'] as $condition)
                        <option value="{{ $condition }}" {{ old('condition') == $condition ? 'selected' : '' }}>{{ $condition }}</option>
                    @endforeach
                </select>
                <p class="sell__form-error-message">
                @error('condition')
                {{ $message }}
                @enderror
                </p>
            </div>
        </div>

        {{-- 商品名と説明 --}}
        <div class="form-section">
            <h2 class="section-title">商品名と説明</h2>

            <div class="form-group">
                <label class="group-label">商品名</label>
                <input type="text" name="name" class="input-box" value="{{ old('name') }}">
                <p class="sell__form-error-message">
                @error('name')
                {{ $message }}
                @enderror
                </p>
            </div>

            <div class="form-group">
                <label class="group-label">ブランド名</label>
                <input type="text" name="brand_name" class="input-box" value="{{ old('brand_name') }}">
                <p class="sell__form-error-message">
                @error('brand_name')
                {{ $message }}
                @enderror
                </p>
            </div>

            <div class="form-group">
                <label class="group-label">商品の説明</label>
                <textarea name="description" class="textarea-box">{{ old('description') }}</textarea>
                <p class="sell__form-error-message">
                @error('description')
                {{ $message }}
                @enderror
                </p>
            </div>
        </div>

        {{-- 販売価格 --}}
        <div class="form-section">
            <h2 class="section-title">販売価格</h2>

            <div class="price-input">
                <span class="yen">¥</span>
                <input type="number" name="price" class="price-box" value="{{ old('price') }}">
            </div>
            <p class="sell__form-error-message">
                @error('price')
                {{ $message }}
                @enderror
                </p>
        </div>

        {{-- 出品ボタン --}}
        <button type="submit" class="submit-button">
            出品する
        </button>

    </form>
</div>

<script>
    document.getElementById('item-image').onchange = function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewContainer = document.getElementById('image-preview');
            const oldImg = previewContainer.querySelector('img');
            if (oldImg) oldImg.remove();
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'contain';
            previewContainer.prepend(img);
            previewContainer.querySelector('label').style.position = 'absolute';
            previewContainer.querySelector('label').style.opacity = '0.7';
        }
        reader.readAsDataURL(file);
    };
</script>

@endsection
