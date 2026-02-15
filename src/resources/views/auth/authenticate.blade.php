@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/authenticate.css')}}">
@endsection

@section('content')
<div class="verify-wrapper">
    <div class="verify-content">
        <p class="verify-text">登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p class="verify-text">メール認証を完了してください。</p>

        <a href="http://localhost:8025" class="verify-btn" target="_blank" rel="noopener noreferrer">認証はこちらから</a>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="resend-link">
                認証メールを再送する
            </button>
        </form>

        @if (session('status') === 'verification-link-sent')
            <p class="success-message">
                認証メールを再送しました。
            </p>
        @endif

    </div>
</div>
@endsection