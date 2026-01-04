@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection


@section('content')

<div class="product-container">
    <div class="left-content">
        <div class="product-visual">
            <div class="product-image-wrapper">
                <img src="{{ asset($product->img_url) }}" class="product-image">
            </div>
            <div class="product-details">
                <h1 class="product-name"> {{ $product->name }}</h1>
                <p class="product-price">￥{{ number_format($product->price) }}<span>(税込)</span></p>
            </div>
        </div>
        <div class="product-group">
            <div class="product-details">
                <p class="product-header">支払い方法</p>
                <select class="payment-method" name="" id="category_id" >
                    <option disabled selected>選択してください</option>
                </select>
            </div>
        </div>
        <div class="product-group">
            <div class="product-details">
                <p class="product-header">配送先
                <a class="product-address__change" href="/purchase/address/{item_id}" >変更する</a>
            </div>
            <div class="product-details">
                <p class="post-code">郵便番号が入る</p>
                <p class="address-building">建物名が入る</p>
            </div>
        </div>
    </div>
    <div class="right-content">
        <div class="meta-list">
            <div class="meta-item">
                <dt class="meta-item__purchase">商品代金</dt>
                <dd class="meta-item__payment">¥{{ $product->price }}</dd>
            </div>
        </div>
        <div class="meta-list">
            <div class="meta-item">
                <dt class="meta-item__purchase">支払い方法</dt>
                <dd class="meta-item__payment">コンビニ払い</dd>
            </div>
        </div>
        <div class="purchase-btn">
            {{-- ここに支払い方法の選択や住所確認などのフォームを実装していきます --}}
            <button class="btn-primary">購入する</button>
        </div>
    </div>
</div>
@endsection