@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')

<div class="container">
    <div class="left-content">
        <div class="item-img">
            <img class="image" src="{{ asset($product->img_url) }}">
        </div>
    </div>

    <div class="right-content">
        <div class="item-content__title">
            <div class="item-title">
                <h1 class="title">{{ $product->name }}</h1>
            </div>
        </div>
        <div class="item-content__brand">
            <div class="item-brand">
                <p class="brand">{{ $product->brand }}</p>
            </div>
        </div>
        <div class="item-content__price">
            <div class="item-price">
                <p class="price">￥{{ $product->price }} (税込)</p>
            </div>
        </div>
        <div class="item-content__button">
            <div class="item-button">
                <button class="item-button__like">
                    <img src="{{ asset('/images/ハートロゴ.デフォルト.png') }}" alt="いいね" class="img-like-icon"/>
                </button>
                <button class="item-button__comment">
                    <img src="{{ asset('/images/ふきだしロゴ.png') }}" alt="コメント" class="img-comment-icon"/>
                </button>
            </div>
        </div>
        <a href="/purchase/{item_id}">購入手続きへ</a>
    </div>
</div>

@endsection