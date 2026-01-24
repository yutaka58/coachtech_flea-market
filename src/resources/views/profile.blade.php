@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection


@section('content')

    <div class="profile-form__content">
        <div class="profile-form__heading">
            <h1>プロフィール設定</h1>
        </div>
        <form class="form" action="/mypage/profile" method="post" enctype="multipart/form-data">
            @csrf
            {{-- 画像保存には enctype="multipart/form-data" が必須 --}}

            <div class="profile-group">
                <div class="profile-image-section">
                    <div class="image-preview">
                        <img id="preview" src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-user.png') }}">
                    </div>
                    <label class="image-upload-label">
                        画像を選択する
                        <input class="select-img" type="file" name="image" id="image-input" accept="image/*">
                    </label>
                </div>
                @error('image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">ユーザー名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" />
                    </div>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">郵便番号</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="post_code" name="post_code" value="{{ old('post_code', $user->post_code) }}" />
                    </div>
                    @error('post_code')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">住所</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="address" name="address" value="{{ old('address', $user->address) }}"/>
                    </div>
                    @error('address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">建物名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="building" name="building" value="{{ old('building', $user->building) }}"/>
                    </div>
                </div>
            </div>

            <div class="form__button">
                <button class="form__button-update" type="submit">更新する</button>
            </div>
        </form>
    </div>


    {{-- プレビュー機能用のJavaScriptを追加 --}}
<script>
document.getElementById('image-input').addEventListener('change', function(e) {
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('preview').src = e.target.result;
    }
    reader.readAsDataURL(e.target.files[0]);
});
</script>

@endsection
