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
                    <img src="{{ asset('/images/ハートロゴ_デフォルト.png') }}" alt="いいね" class="img-like-icon"/>
                    <p>いいね</p><!-- いいねの数を表示せる処理 -->
                </button>

                <button class="item-button__comment">
                    <img src="{{ asset('/images/ふきだしロゴ.png') }}" alt="コメント" class="img-comment-icon"/>
                    <p>コメント</p><!-- コメントの数を表示せる処理 -->
                </button>
            </div>
        </div>
        <div class="item-purchase">
            <div class="item-purchase__grid">
                <a class="item-purchase__btn" href="/purchase/{item_id}">購入手続きへ</a>
            </div>
        </div>
        <div class="item-content__subtitle">
            <div class="item-subtitle">
                <h2 class="subtitle">商品説明</h2>
            </div>
        </div>
        <div class="item-content__subtitle">
            <div class="item-subtitle">
                <h2 class="subtitle">商品の情報</h2>
            </div>
        </div>
        <div class="item-content__comment">
            <div class="item-comment">
                <p class="comment">コメント</p><span>(コメント数を表示させる)</span>
            </div>
        </div>
        <div class="item-content__comment-input">
            <div class="item-comment__input-field">
                <label class="comment-input" for="comment">商品へのコメント</label>
                <textarea name="comment" id=""></textarea>
            </div>
        </div>
        <div class="comment-send">
            <div class="comment-btn">
                <a class="comment-send__btn" href="/purchase/{item_id}">コメントを送信する</a>
            </div>
        </div>

    </div>
</div>

@endsection