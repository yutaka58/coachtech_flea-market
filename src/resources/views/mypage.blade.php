@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection


@section('content')

<div class="top-page__content">
    <div class="tab-menu">
        <form action="/mypage" method="get" class="tab-form">
            <div class="user-info">
                <div class="user-avatar">
                    <img class="user-avatar__img" src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-user.png') }}">
                        <h1 class="user-avatar__name">{{ $user?->name }}</h1>
                    </img>
                </div>
            </div>
            <div class="mypage-edit">
                <a class="mypage-link" href="/mypage/profile">
                    <button class="mypage-btn">プロフィールを編集</button>
                </a>
            </div>
        </form>
        
            <a href="" class="tab-item">出品した商品</a>

            <a href="" class="tab-item">購入した商品</a>

    </div>


@endsection
