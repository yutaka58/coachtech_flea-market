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
                    <img class="user-avatar__img" src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-user.png') }}">
                    <span class="user-avatar__name">{{ $user?->name }}</span>
                </div>
                <a class="mypage-btn-link" href="/mypage/profile">プロフィールを編集</a>
            </div>
        </form>
        <div class="tab-items">
            <a href="/mypage?tab=sell" class="tab-item {{ $tab == 'sell' ? 'active' : '' }}">出品した商品</a>
            <a href="/mypage?tab=buy" class="tab-item {{ $tab == 'buy' ? 'active' : '' }}">購入した商品</a>
        </div>
    </div>
    <div class="item-content">
        @if($tab == 'sell')
            {{-- 出品した商品の一覧 --}}
            <div class="item-grid">
                @foreach($sellItems as $item)
                    <div class="item-card">
                        <img class="item-img" src="{{ asset($item->img_url) }}">
                        <p>{{ $item->name }}</p>
                    </div>
                @endforeach
            </div>
        @else
            {{-- 購入した商品の一覧 --}}
            <div class="item-grid">
                @foreach($buyItems as $item)
                    <div class="item-card">
                        <img class="item-img" src="{{ asset($item->img_url) }}">
                        <p>{{ $item->name }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
