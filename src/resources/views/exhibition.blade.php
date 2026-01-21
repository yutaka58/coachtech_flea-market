@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/exhibition.css') }}">
@endsection


@section('content')


    <div class="sell-form__content">
        <div class="sell-form__heading">
            <h1>商品の出品</h1>
        </div>
        <form class="form" action="/exhibition" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">商品画像</span>
                </div>
                <div class="form-grid">
                    <div class="image-preview">
                        <img id="preview" src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-user.png') }}">
                    </div>
                    <label class="image-upload-label">
                        画像を選択する
                        <input class="select-img" type="file" name="image" id="image-input" accept="image/*">
                    </label>
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label--header">商品の詳細</h2>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">カテゴリー</span>
                </div>
                <div class="form__group-content">
                    <div class="category-tag-container">
                        @foreach($categories as $category)
                            <label class="category-tag">
                                <input type="checkbox" name="category_id[]" value="{{ $category->id }}" {{ is_array(old('category_id')) && in_array($category->id, old('category_id')) ? 'checked' : '' }}>
                                <span class="tag-label">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="form__group-title">
                    <span class="form__label--item">商品の状態</span>
                </div>
                <div class="form__select-content">
                    <select class="form__input--condition" name="condition" id="condition">
                        <option value="" disabled selected hidden>選択してください</option>
                        <option value="very_good">良好</option>
                        <option value="good">目立った傷や汚れなし</option>
                        <option value="bad">やや傷や汚れあり</option>
                        <option value="very_bad">状態が悪い</option>
                    </select>
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label--header">商品名と説明</h2>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">商品名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name') }}" />
                    </div>
                </div>
                <div class="form__group-title">
                    <span class="form__label--item">ブランド名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="brand" value="{{ old('brand') }}" />
                    </div>
                </div>
                <div class="form__group-title">
                    <span class="form__label--item">商品の説明</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--description">
                        <input type="text" name="description" value="{{ old('description') }}" />
                    </div>
                </div>
                <div class="form__group-title">
                    <span class="form__label--item">販売価格</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="number" name="price" value="{{ old('price') }}" />
                    </div>
                </div>
            </div>

            <div class="form__button">
                <button class="form__button-submit" type="submit">出品する</button>
            </div>
        </form>
    </div>

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
