@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css')}}">
@endsection

@section('content')
<div class="profile-container">
    <h1 class="profile-title">プロフィール設定</h1>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- プロフィール画像 --}}
        <div class="profile-image-area">
            <div class="profile-image">
                @if ($user->image)
                    <img src="{{ asset('storage/' . $user->image) }}" alt="プロフィール画像">
                @else
                    <div class="profile-image-placeholder"></div>
                @endif
            </div>

            <label class="image-select-button" for="image_input">
                画像を選択する
                <input type="file" name="image" id="image_input" style="display:none;">
            </label>
        </div>
        <p class="profile-error-message">
            @error('image')
            {{ $message }}
            @enderror
        </p>
        {{-- ユーザー名 --}}
        <div class="form-group">
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
        </div>
        <p class="profile-error-message">
            @error('name')
            {{ $message }}
            @enderror
        </p>

        {{-- 郵便番号 --}}
        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
        </div>
        <p class="profile-error-message">
            @error('postal_code')
            {{ $message }}
            @enderror
        </p>

        {{-- 住所 --}}
        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
        </div>
        <p class="profile-error-message">
            @error('address')
            {{ $message }}
            @enderror
            </p>
        {{-- 建物名 --}}
        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}">
        </div>

        {{-- 更新ボタン --}}
        <button type="submit" class="submit-button">
            更新する
        </button>
    </form>
</div>

<script>
document.getElementById('image_input').onchange = function (e) {
    const reader = new FileReader();
    reader.onload = function (e) {
        const previewArea = document.querySelector('.profile-image');
        const preview = previewArea.querySelector('img') || previewArea.querySelector('.profile-image-placeholder');
        if (preview.classList.contains('profile-image-placeholder')) {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.replaceWith(img);
        } else {
            preview.src = e.target.result;
        }
        }
        if (e.target.files[0]) {
            reader.readAsDataURL(e.target.files[0]);
        }
};
</script>
@endsection
