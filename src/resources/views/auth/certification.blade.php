<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech_flea-market-app</title>

    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/certification.css') }}">

</head>
<body>
    <div class="header">
        <div class="header-content">
            <a class="top-page" href="/">
                <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECHロゴ">
            </a>
        </div>
    </div>

    <div class="main-container">
        <div class="certification-form">
            <div class="certification-form__content">
                <p class="certification-mail">登録していただいたメールアドレスに認証メールを送付しました。</p>
                <p class="certification-mail">メール認証を完了してください。</p>
            </div>

            <form class="form" action="/certification" method="get">
                <div class="certification-btn">
                    <a href="http://localhost:8025" target="_blank" class="certification-btn__content">
                        認証はこちらから
                    </a>
                </div>
            </form>

            <div class="resend-mail">
                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button type="submit" class="resend-link">認証メールを再送する</button>
                </form>
            </div>
        </div>
    </div>
</body>
