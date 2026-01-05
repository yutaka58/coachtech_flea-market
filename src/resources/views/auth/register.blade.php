<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech_flea-market-app</title>

    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">

</head>
<body>
    <div class="header">
        <div class="header-content">
            <a class="top-page" href="/">
                <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECHロゴ">
            </a>
        </div>
    </div>
    
    <div class="register-form__content">
        <div class="register-form__heading">
            <h1>会員登録</h1>
        </div>
        <form class="form" action="/register" method="post" novalidate>
            @csrf

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">ユーザー名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name') }}" />
                    </div>
                    @error('name')
                        <div class="error-message" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">メールアドレス</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="email" name="email" value="{{ old('email') }}" />
                    </div>
                    @error('email')
                        <div class="error-message" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">パスワード</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="password" name="password" />
                    </div>
                    @error('password')
                        <div class="error-message" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">確認用パスワード</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="password" name="password_confirmation" />
                    </div>
                    @error('password_confirmation')
                        <div class="error-message" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__button">
                <button class="form__button-submit" type="submit">登録する</button>
            </div>
        </form>
        <div class="login__link">
            <a class="login__button-submit" href="/login">ログインの方はこちら</a>
        </div>
    </div>

</body>
</html>

