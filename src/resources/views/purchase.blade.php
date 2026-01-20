@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection


@section('content')

<div class="product-container">
    <div class="left-content">
        <div class="product-visual">
            <div class="product-image-wrapper">
                <img src="{{ asset($item->img_url) }}" class="product-image">
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
                <p class="address-building">{{ Auth::user()->address }}　　{{ Auth::user()->building }}</p>
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
            <form action="/purchase/{{ $item->id }}" method="post" id="purchase-form">
                @csrf
                <input type="hidden" name="payment-method" id="hidden-payment__method">
                <button class="btn-primary" type="submit">購入する</button>
            </form>
        </div>
    </div>
</div>


<!-- 左で選択した支払い方法を右にリアルタイムで表示 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('payment_method_select');
    const display = document.getElementById('display_payment_method');
    const hiddenInput = document.getElementById('hidden-payment__method');

    function updateView(methodText, methodValue) {
        if (methodValue !== "") {
            display.innerText = methodText;
            hiddenInput.value = methodValue; // 購入用フォームのhidden値も更新
        }
    }

    // 初期表示
    updateView(select.options[select.selectedIndex].text, select.value);

    select.addEventListener('change', function() {
        const methodValue = this.value;
        const methodText = this.options[this.selectedIndex].text;
        const url = "{{ route('payment.save_session', ['item_id' => $item->id]) }}";

        // セッション保存を実行
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ payment_method: methodValue })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 保存に成功してから見た目を更新
                updateView(methodText, methodValue);
                console.log('支払い方法をセッションに保持しました');
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

@endsection