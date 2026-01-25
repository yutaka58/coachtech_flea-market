@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection


@section('content')

<form action="/purchase/{{ $item->id }}" method="post" id="purchase-form">
@csrf

    <div class="product-container">
        <div class="left-content">
            <div class="product-visual">
                <div class="product-image">
                    <img class="image" src="{{ str_starts_with($item->img_url, 'http') ? $item->img_url : asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                </div>
                <div class="product-details">
                    <h1 class="product-name"> {{ $item->name }}</h1>
                    <p class="product-price">￥{{ number_format($item->price) }}<span>(税込)</span></p>
                </div>
            </div>
            <div class="product-group">
                <div class="product-details">
                    <p class="product-header">支払い方法</p>
                    <select class="payment-method select" name="payment_method" id="payment_method_select">
                        <option value="" disabled {{ is_null($payment_id) ? 'selected' : '' }} hidden>選択してください</option>
    
                        <option value="konbini" {{ $payment_id == 'konbini' ? 'selected' : '' }}>コンビニ払い</option>
    
                        <option value="card" {{ $payment_id == 'card' ? 'selected' : '' }}>カード支払い</option>
                    </select>
                    @error('payment_method')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                </div>
            </div>
            <div class="product-group">
                <div class="product-details">
                    <p class="product-header">配送先
                        <a class="product-address__change" href="/purchase/address/{{ $item->id }}">変更する</a>
                    </p>
                </div>
                <div class="product-details">
                    <p class="post-code">〒 {{ Auth::user()->post_code }}</p>
                    <input type="hidden" name="post_code" value="{{ old('post_code', Auth::user()->post_code) }}">
                    @error('post_code')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <p class="address-building">{{ Auth::user()->address }}　　{{ Auth::user()->building }}</p>
                    <input type="hidden" name="address" value="{{ old('address', Auth::user()->address) }}">
                    <input type="hidden" name="building" value="{{ old('building', Auth::user()->building) }}">
                    @error('address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    @error('building')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="right-content">
            <dl class="meta-list">
                <div class="meta-item">
                    <dt class="meta-item__purchase">商品代金</dt>
                    <dd class="meta-item__payment">￥{{ number_format($item->price) }}</dd>
                </div>
            </dl>
            <dl class="meta-list">
                <div class="meta-item">
                    <dt class="meta-item__purchase">支払い方法</dt>
                    <dd id="display_payment_method" class="meta-item__payment">{{ $payment_id ?? '未選択' }}</dd>
                </div>
            </dl>
            <div class="purchase-btn">
                <input type="hidden" name="payment_method" id="hidden_payment_method">
                <button class="btn-primary" type="submit">購入する</button>
            </div>
        </div>
    </div>
</form>


<!-- 左で選択した支払い方法を右にリアルタイムで表示 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('payment_method_select');
    const display = document.getElementById('display_payment_method');
    const hiddenInput = document.getElementById('hidden_payment_method'); // IDがアンダースコアになっているか確認
    const form = document.getElementById('purchase-form');

    // 表示と隠し入力を更新する共通関数
    function updateView() {
        const methodValue = select.value;
        const methodText = select.options[select.selectedIndex].text;

        if (methodValue && methodValue !== "") {
            display.innerText = methodText;
            hiddenInput.value = methodValue; // ここでhiddenに値を確実にセット
        }
    }

    // 1. ページ読み込み時に実行（初期状態の反映）
    updateView();

    // 2. セレクトボックスが変わった時に実行（セッション保存）
    select.addEventListener('change', function() {
        updateView();

        const url = "{{ route('payment.save_session', ['item_id' => $item->id]) }}";
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ payment_method: this.value })
        });
    });

    // 3. 💡 【追加】送信ボタンが押された瞬間に最終確認
    form.addEventListener('submit', function() {
        updateView();
    });
});

</script>

@endsection