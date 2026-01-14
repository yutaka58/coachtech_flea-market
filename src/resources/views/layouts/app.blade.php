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
            <a class="top-page" href="/">
                <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECHロゴ">
            </a>

            <form class="search-form" action="/" method="get">
                <input type="hidden" name="tab" value="{{ $tab ?? 'recommend' }}">
                <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{request('keyword')}}">
            </form>

            <ul class="header-nav">
                @if (Auth::check())
                    {{-- ログイン中の表示 --}}
                    <li class="header-nav__item">
                        <form action="/logout" method="post">
                            @csrf
                            <button class="header-nav__link-button" type="submit" style="background:none; border:none; cursor:pointer;">ログアウト</button>
                        </form>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="/mypage">マイページ</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__exhibition-button" href="/exhibition">出品</a>
                    </li>
                @else
                    {{-- 未ログイン（ゲスト）時の表示：ここを追加して構造を維持する --}}
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="/login">ログイン</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="/mypage">マイページ</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__exhibition-button" href="/exhibition">出品</a>
                    </li>

                @endif
            </ul>
        </div>
    </div>

    @yield('content')
</body>
</html>