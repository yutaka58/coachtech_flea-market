@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection


@section('content')

<div class="product-container">
    <div class="product-visual">
        <div class="product-image-wrapper">
            <img src="{{ asset($product->img_url) }}" class="product-image">
        </div>
        <div class="product-details">
            <h1 class="product-name"> {{ $product->name }}</h1>
            <p class="product-price">￥{{ number_format($product->price) }}<span>(税込)</span></p>
        </div>
    </div>
    <div class="product-visual">
        <div class="product-details">
            <p class="product-header">支払方法</p>
            <select class="payment-method" name="" id="" >選択してください</select>
        </div>
    </div>
    <div class="product-visual">
        <div class="product-details">
            <p class="product-header">配送先
            <a class="address-change" href="/purchase/address/{item_id}" >変更する</a>
        </div>
        <div class="">

        </div>
    </div>



    {{-- ここに支払い方法の選択や住所確認などのフォームを実装していきます --}}
    <button class="btn-primary">購入する</button>
</div>
@endsection