@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css')}}">
@endsection

@section('content')
<div class="login-container">
    <h2 class="login__form-title">ログイン</h2>
    <form class="login__form" action="/login" method="post">
        @csrf
        <div class="login__form-group">
            <label class="login__form-label" for="email">メールアドレス</label>
            <input class="login__form-input" type="mail" name="email" id="email" value="{{ old('email') }}">
            <p class="login__form-error-message">
            @error('email')
            {{ $message }}
            @enderror
            </p>
        </div>
        <div class="login__form-group">
            <label class="login__form-label" for="password">パスワード</label>
            <input class="login__form-input" type="password" name="password" id="password">
            <p class="login__form-error-message">
            @error('password')
            {{ $message }}
            @enderror
            </p>
        </div>
        <input class="login__form-btn" type="submit" value="ログインする">
    </form>
    <div class="register-link">
        <a class="link" href="{{ route('register')}}">会員登録はこちら</a>
    </div>
</div>
@endsection
