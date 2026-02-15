@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css')}}">
@endsection

@section('content')
<div class="register__form">
    <h2 class="register__form-title">会員登録</h2>
    <div class="register-form__inner">
        <form class="register-form__form" action="{{ route('register')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="register__form-group">
            <label class="register__form-label" for="name">ユーザー名</label>
            <input class="register__form-input" type="text" name="name" id="name" value="{{ old('name') }}">
            <p class="register__form-error-message">
            @error('name')
            {{ $message }}
            @enderror
            </p>
        </div>
        <div class="register__form-group">
            <label class="register__form-label" for="email">メールアドレス</label>
            <input class="register__form-input" type="mail" name="email" id="email" value="{{ old('email') }}">
            <p class="register__form-error-message">
            @error('email')
            {{ $message }}
            @enderror
            </p>
        </div>
        <div class="register__form-group">
            <label class="register__form-label" for="password">パスワード</label>
            <input class="register__form-input" type="password" name="password" id="password">
            <p class="register__form-error-message">
            @error('password')
            {{ $message }}
            @enderror
            </p>
        </div>
        <div class="register__form-group">
            <label class="register__form-label" for="password_confirmation">確認用パスワード</label>
            <input class="register__form-input" type="password_confirmation" name="password_confirmation" id="password_confirmation">
            <p class="register__form-error-message">
            @error('password_confirmation')
            {{ $message }}
            @enderror
            </p>
        </div>
        <input class="register__form-btn" type="submit" value="登録する">
        </form>
        <div class="login-link">
            <a class="link" href="{{ route('login')}}">ログインはこちら</a>
        </div>
    </div>
</div>
@endsection
