<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech_flea-market-app</title>

    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')

</head>
<body>
    <div class="header">
        <div class="header-content">
            <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECHロゴ">

            <form class="search-form" action="/search" method="get">
                @csrf
                <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{request('keyword')}}">
            </form>

            <ul class="header-nav">
                @if (Auth::check())
                <li class="header-nav__item">
                    <form action="/logout" method="post">
                        @csrf
                        <a class="header-nav__link" href="/login">ログアウト</a>
                    </form>
                </li>
                <li class="header-nav__item">
                    <a class="header-nav__link" href="/mypage">マイページ</a>
                </li>
                <li class="header-nav__item">
                    <a class="header-nav__sell-button" href="/sell">出品</a>
                </li>
                @endif
            </ul>
        </div>
    </div>

    @yield('content')
</body>
</html>