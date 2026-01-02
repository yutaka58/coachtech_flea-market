@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="product-container">
    {{-- 左側：商品画像 --}}
    <div class="product-visual">
        <div class="product-image-wrapper">
            <img src="{{ asset($product->img_url) }}" alt="{{ $product->name }}" class="product-image">
        </div>
    </div>

    {{-- 右側：商品詳細 --}}
    <div class="product-details">
        <div class="product-header">
            <h1 class="product-name">{{ $product->name }}</h1>
            <p class="product-brand">{{ $product->brand }}</p>
            <p class="product-price">￥{{ number_format($product->price) }}<span>(税込)</span></p>
        </div>

        <div class="product-actions">
            <div class="action-item">
                <button class="icon-button"><img src="{{ asset('/images/ハートロゴ_デフォルト.png') }}" alt="いいね"></button>
                <span class="count">3</span>
            </div>
            <div class="action-item">
                <button class="icon-button"><img src="{{ asset('/images/ふきだしロゴ.png') }}" alt="コメント"></button>
                <span class="count">1</span>
            </div>
        </div>

        <div class="purchase-action">
            <a href="/purchase/{{ $product->id }}" class="btn-purchase">購入手続きへ</a>
        </div>

        {{-- 各セクション --}}
        <section class="info-section">
            <h2 class="section-title">商品説明</h2>
            <div class="description-content">
                <p>カラー：グレー</p>
                <p>新品</p>
                <p>商品の状態は良好です。傷もありません。</p>
                <p>購入後、即発送いたします。</p>
            </div>
        </section>

        <section class="info-section">
            <h2 class="section-title">商品の情報</h2>
            <dl class="meta-list">
                <div class="meta-item">
                    <dt>カテゴリー</dt>
                    <dd class="tag-group">
                        {{-- 商品に紐づくカテゴリーをループで回して表示 --}}
                        @foreach($product->categories as $category)
                            <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                    </dd>
                </div>
                <div class="meta-item">
                    <dt>商品の状態</dt>
                    <dd>良好</dd>
                </div>
            </dl>
        </section>

        <section class="info-section">
            <h2 class="section-title">コメント(1)</h2>
            <div class="comment-item">
                <div class="user-info">
                    <div class="user-avatar"></div>
                    <span class="user-name">admin</span>
                </div>
                <div class="comment-body">
                    こちらにコメントが入ります。
                </div>
            </div>
        </section>

        <section class="info-section">
            <form action="#" method="POST" class="comment-form">
                @csrf
                <label for="comment" class="form-label">商品へのコメント</label>
                <textarea name="comment" id="comment" class="comment-textarea"></textarea>
                <button type="submit" class="btn-submit">コメントを送信する</button>
            </form>
        </section>
    </div>
</div>
@endsection