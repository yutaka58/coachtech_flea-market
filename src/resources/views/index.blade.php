@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<div class="top-page__content">
    <div class="tab-menu">
        <form action="/?tab=mylist" method="GET" class="tab-form">
            <!-- おすすめがリンク不要の場合[label]使用 -->
            <label class="tab-item">おすすめ</label>

            {{-- マイリストボタン --}}
            <button type="submit" name="tab" value="mylist"
                class="tab-item-button {{ $tab == 'mylist' ? 'active' : '' }}">
                マイリスト
            </button>
        </form>
    </div>

    <div class="item-grid">
        @forelse ($products as $product)
            <div class="item-card">
                <a href="/item/{{ $product->id }}">
                    <div class="item-image">
                        <img class="image" src="{{ asset($product->img_url) }}" alt="{{ $product->name }}">
                        {{-- ここに SOLD 表示を追加 --}}
                        @if($product->order)
                            <div class="sold-label">
                                <span>SOLD</span>
                            </div>
                        @endif
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
