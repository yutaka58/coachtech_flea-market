@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection


@section('content')

    <div class="profile-form__content">
        <div class="profile-form__heading">
            <h1>プロフィール設定</h1>
        </div>
        <form class="form" action="/mypage/profile" method="post" novalidate>
            @csrf

            <div class="profile-group">
                <div class="profile-image-section">
                    <div class="image-preview">
                        <img id="preview" src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-user.png') }}">
                    </div>
        
                    <label class="image-upload-label">
                        画像を選択する
                        <input type="file" name="image" id="image-input" accept="image/*" style="display: none;">
                    </label>
                </div>
            </div>

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
                    <span class="form__label--item">郵便番号</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="code" name="code" value="{{ old('code') }}" />
                    </div>
                    @error('code')
                        <div class="error-message" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">住所</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="address" name="address" />
                    </div>
                    @error('address')
                        <div class="error-message" style="color: red;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">建物名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="building" name="building" />
                    </div>
                </div>
            </div>

            <div class="form__button">
                <button class="form__button-update" type="update">更新する</button>
            </div>
        </form>
    </div>

@endsection
