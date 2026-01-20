@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')

<div class="address-form__content">
    <div class="address-form__heading">
        <h1>住所の変更</h1>
    </div>
    <form class="form" action="/purchase/address/{{ $item->id }}" method="post">
        @csrf

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="post_code" name="post_code" value="{{ old('post_code', Auth::user()->post_code) }}" />
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
                    <input type="address" name="address" value="{{ old('address', Auth::user()->address) }}" />
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
                    <input type="building" name="building" value="{{ old('building', Auth::user()->building) }}"/>
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>

@endsection
