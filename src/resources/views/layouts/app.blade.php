<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Flea-Market</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/sanitize.css')}}">
        <link rel="stylesheet" href="{{ asset('css/common.css')}}">
        @yield('css')
    </head>
    <body>
        @php
            // ヘッダーを表示しない画面
            $hideHeaderRoutes = [
                'login',
                'register',
                'verification.notice',
                'authenticate',
            ];
        @endphp

        <div class="app">
            <header class="header">
                <div class="header__inner">
                    <!-- ロゴ -->
                    <div class="header__logo">
                        <a href="{{ route('items.index') }}">
                            <img src="{{ asset('storage/logos/COACHTECHヘッダーロゴ.png')}}" alt="ロゴ">
                        </a>
                    </div>

                @if (!in_array(Route::currentRouteName(), $hideHeaderRoutes))
                    <!-- 検索フォーム -->
                    <form action="{{ route('items.index') }}" method="GET" class="search__form">
                        <input class="search__form-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                    </form>

                    <!-- ナビゲーション -->
                    <nav class="nav__link">
                    @auth
                        <!-- ログイン後 -->
                        <form class="nav__link-logout" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="nav__link-logout-btn" type="submit">ログアウト</button>
                        </form>
                        <a class="nav__link-mypage" href="{{ route('mypage') }}">マイページ</a>
                        <a class="nav__link-sell-btn" href="{{ route('sell.create') }}">出品</a>

                    @else
                    <!-- 未ログイン -->
                        <a href="{{ route('login') }}" class="nav__link-login">ログイン</a>
                        <a href="{{ route('login') }}" class="nav__link-mypage">マイページ</a>
                        <a href="{{ route('login') }}" class="nav__link-sell-btn">出品</a>
                    @endauth
                    </nav>
                @endif
                </div>
            </header>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </body>
</html>
