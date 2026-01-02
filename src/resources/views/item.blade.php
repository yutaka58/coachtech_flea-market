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
                <button class="icon-button btn-favorite" data-product-id="{{ $product->id }}">
                    <img src="{{ asset($product->isFavoritedBy(auth()->user()) ? '/images/ハートロゴ_ピンク.png' : '/images/ハートロゴ_デフォルト.png') }}" id="favorite-icon" alt="いいね"></button>
                <span class="count" id="favorite-count">{{ $product->favorites->count() }}</span>

            </div>
            <div class="action-item">
                <span class="icon-logo"><img src="{{ asset('/images/ふきだしロゴ.png') }}" alt="コメント"></span>
                <span class="count" id="comment-count">{{ $product->comments?->count() ?? 0 }}</span>
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
            <h2 class="section-title">コメント({{ $product->comments->count() }})</h2>
    
            @foreach($product->comments as $comment)
                <div class="comment-item">
                    <div class="user-info">
                        <div class="user-avatar">
                            {{-- プロフィール画像がある場合 --}}
                            @if($comment->user->img_url)
                                <img src="{{ asset($comment->user->img_url) }}">
                            @endif
                        </div>
                        <span class="user-name">{{ $comment->user->name }}</span>
                    </div>
                    <div class="comment-body">
                        {{ $comment->comment }}
                    </div>
                </div>
            @endforeach
        </section>

        <section class="info-section">
            <form action="/item/{{ $product->id }}/comment" method="POST" class="comment-form">
                @csrf
                <label for="comment" class="form-label">商品へのコメント</label>
                <textarea name="comment" id="comment" class="comment-textarea"></textarea>
                @error('comment')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                <button type="submit" class="btn-submit">コメントを送信する</button>
            </form>
        </section>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded', function() {
    // 全ての「いいね」ボタンを取得（念のため個別ではなく全体で取得）
    const favoriteBtn = document.querySelector('.btn-favorite');
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function(e) {
            e.preventDefault(); // ボタンのデフォルト動作を防止

            const productId = this.dataset.productId;
            const icon = document.getElementById('favorite-icon');
            const countSpan = document.getElementById('favorite-count');

            // URLを確実に作成（スラッシュの有無に注意）
            fetch('/products/' + productId + '/favorite', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data) {
                    // 数値とアイコンの更新
                    countSpan.textContent = data.count;
                    icon.src = data.liked 
                        ? "{{ asset('/images/ハートロゴ_ピンク.png') }}" 
                        : "{{ asset('/images/ハートロゴ_デフォルト.png') }}";
                }
            })
            .catch(error => console.error('Error:', error));
        });
    } else {
        console.error('いいねボタンが見つかりません。クラス名を確認してください。');
    }
});
</script>
                
@endsection


