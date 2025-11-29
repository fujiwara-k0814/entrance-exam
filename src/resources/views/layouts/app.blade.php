<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <title>FleaMarketApp</title>
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-logo__content">
                <a href="/" class="header-logo__link">
                    <img src="/images/logo.svg" alt="ロゴ画像" class="header-logo">
                </a>
            </div>
            @if (!in_array(Route::currentRouteName(), ['login', 'register', 'verification.notice', 'user.message']))
                <form action="/" method="get" class="header__search-form">
                    @if (request()->query('tab') === 'mylist')
                        <input type="hidden" name="tab" value="mylist">
                    @endif
                    <input type="text" name="keyword" class="header__search-input" placeholder="なにをお探しですか？" 
                        value="{{ request('keyword') }}">
                </form>
                <nav class="header__nav">
                    <ul class="header__nav-list">
                        <li>
                            @if (Auth::check())
                                <form action="/logout" method="post">
                                    @csrf
                                    <button type="submit" class="header__nav-button">ログアウト</button>
                                </form>
                            @else
                                <a href="/login" class="header__nav-button">ログイン</a>
                            @endif
                        </li>
                        <li>
                            <a href="/mypage" class="header__mypage-button">マイページ</a>
                        </li>
                        <li>
                            <a href="/sell" class="header__sell-button">出品</a>
                        </li>
                    </ul>
                </nav>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>