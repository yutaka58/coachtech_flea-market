@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection


@section('content')

<div class="top-page__content">
    <div class="tab-menu">
        <form action="/mypage" method="get" class="user-form">
            <div class="user-info">
                <div class="user-avatar">
                    <img class="user-avatar__img" src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-user.png') }}"></img>
                    <span class="user-avatar__name">{{ $user?->name }}</span>
                </div>
            </div>
            <div class="user-info">
                <a class="mypage-link" href="/mypage/profile">
                    <button class="mypage-btn">プロフィールを編集</button>
                </a>
            </div>
        </form>
        <form class="tab-form" action="">
            <a href="" class="tab-item">出品した商品</a>
            <a href="" class="tab-item">購入した商品</a>
        </form>
    </div>
    <div class="item-grid">
        @forelse ($items as $item)
            <div class="item-card">
                <a href="/item/{{ $item->id }}">
                    <div class="item-image">
                        <img class="image" src="{{ asset($item->img_url) }}" alt="{{ $item->name }}">
                        {{-- ここに SOLD 表示を追加 --}}
                        @if($item->order)
                            <div class="sold-label">
                                <span>SOLD</span>
                            </div>
                        @endif
                    </div>
                    <p class="item-name">{{ $item->name }}</p>
                </a>
            </div>
        @empty
            <p>表示する商品がありません。</p>
        @endforelse
    </div>

</div>

@endsection
