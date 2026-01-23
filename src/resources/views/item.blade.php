@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="product-container">
    {{-- 左側：商品画像 --}}
    <div class="product-visual">
        <div class="product-image">
            <img class="image" src="{{ str_starts_with($item->img_url, 'http') ? $item->img_url : asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
        </div>
    </div>

    {{-- 右側：商品詳細 --}}
    <div class="product-details">
        <div class="product-header">
            <h1 class="product-name">{{ $item->name }}</h1>
            <p class="product-brand">{{ $item->brand }}</p>
            <p class="product-price">￥{{ number_format($item->price) }}<span>(税込)</span></p>
        </div>

        <div class="product-actions">
            <div class="action-item">
                <button class="icon-button btn-favorite" data-product-id="{{ $item->id }}">
                    <img src="{{ asset($item->isFavoritedBy(auth()->user()) ? '/images/ハートロゴ_ピンク.png' : '/images/ハートロゴ_デフォルト.png') }}" id="favorite-icon" alt="いいね"></button>
                <span class="count" id="favorite-count">{{ $item->favorites->count() }}</span>

            </div>
            <div class="action-item">
                <span class="icon-logo"><img src="{{ asset('/images/ふきだしロゴ.png') }}" alt="コメント"></span>
                <span class="count" id="comment-count">{{ $item->comments?->count() ?? 0 }}</span>
            </div>
        </div>

        <div class="purchase-action">
            {{-- $item->order(リレーション)、あたは商品のステータスで判定 --}}
            @if($item->order)
                {{-- 売り切れの場合：aタグではなくspanなどでボタン風に表示 --}}
                <span class="btn-purchase btn-disabled">売り切れ</span>
            @else
                {{-- 在庫がある場合：通常のリンク --}}
                <a href="/purchase/{{ $item->id }}" class="btn-purchase">購入手続きへ</a>
            @endif
        </div>

        {{-- 各セクション --}}
        <section class="info-section">
            <h2 class="section-title">商品説明</h2>
            <div class="description-content">
                {{ $item->description }}
            </div>
        </section>

        <section class="info-section">
            <h2 class="section-title">商品の情報</h2>
            <dl class="meta-list">
                <div class="meta-item">
                    <dt>カテゴリー</dt>
                    <dd class="tag-group">
                        {{-- 商品に紐づくカテゴリーをループで回して表示 --}}
                        @foreach($item->categories as $category)
                            <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                    </dd>
                </div>
                <div class="meta-item">
                    <dt>商品の状態</dt>
                    <dd>
                        @php
                            $conditions = [
                                'very_good' => '良好',
                                'good'      => '目立った傷や汚れなし',
                                'bad'       => 'やや傷や汚れあり',
                                'very_bad'  => '状態が悪い',
                            ];
                        @endphp
                        {{ $conditions[$item->condition] ?? $item->condition }}
                    </dd>
                </div>
            </dl>
        </section>

        <section class="info-section">
            <h2 class="section-title">コメント({{ $item->comments->count() }})</h2>
    
            @foreach($item->comments as $comment)
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
            <form action="/item/{{ $item->id }}/comment" method="POST" class="comment-form">
                @csrf
                <label for="comment" class="form-label">商品へのコメント</label>
                <textarea name="comment" id="comment" class="comment-textarea"></textarea>
                @error('comment')
                    <p style="color: red">{{ $message }}</p>
                @enderror
                @if($item->order)
                    <span class="btn-submit btn-disabled">コメントを送信する</span>
                @else
                    <button type="submit" class="btn-submit">コメントを送信する</button>
                @endif
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


