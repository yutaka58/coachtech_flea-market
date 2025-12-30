@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<div class="top-page__content">
    <div class="tab-menu">
        <!-- おすすめがリンク不要の場合[label]使用 -->
        <label class="tab-item">おすすめ</label>

        <a href="/?tab=recommend" class="tab-item {{ $tab == 'recommend' ? 'active' : '' }}">おすすめ</a>
        <a href="/?tab=mylist" class="tab-item {{ $tab == 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    <div class="item-grid">
        @forelse ($products as $product)
            <div class="item-card">
                <a href="/item/{{ $product->id }}">
                    <div class="item-image">
                        <img class="image" src="{{ asset($product->img_url) }}" alt="{{ $product->name }}">
                    </div>
                    <p class="item-name">{{ $product->name }}</p>
                </a>
            </div>
        @empty
            <p>表示する商品がありません。</p>
        @endforelse
    </div>
</div>

@endsection
