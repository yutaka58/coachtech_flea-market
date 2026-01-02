@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/profile.css') }}">
@endsection


@section('content')
<div class="container">
    <h1>購入画面</h1>
    <div class="product-info">
        <img src="{{ asset($product->img_url) }}" width="200">
        <h2>{{ $product->name }}</h2>
        <p>価格：￥{{ number_format($product->price) }}</p>
    </div>
    
    {{-- ここに支払い方法の選択や住所確認などのフォームを実装していきます --}}
    <button class="btn-primary">購入する</button>
</div>
@endsection