@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<div class="top-page__content">
    <div class="tab-menu">
        <form action="/?tab=mylist" method="get" class="tab-form">
            <!-- おすすめがリンク不要の場合[label]使用
            <label class="tab-item">おすすめ</label>
             -->

            {{-- 現在の検索キーワードを保持 --}}
            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
            
            <a href="/?tab=recommend&keyword={{ request('keyword') }}"
                class="tab-item {{ $tab == 'recommend' ? 'active' : '' }}">おすすめ</a>

            <a href="/?tab=mylist&keyword={{ request('keyword') }}"
                class="tab-item {{ $tab == 'mylist' ? 'active' : '' }}">マイリスト</a>
        </form>
    </div>

    <div class="item-grid">
        @forelse ($items as $item)
            <div class="item-card">
                <a href="/item/{{ $item->id }}">
                    <div class="item-image">
                        <img class="image" src="{{ str_starts_with($item->img_url, 'http') ? $item->img_url : asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
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
