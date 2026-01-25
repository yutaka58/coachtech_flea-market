<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Category;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword'); // 検索窓からの値

        // 1. まずは「全商品」をベースにする
        $query = Item::query();

        // 2. 検索ワードがあれば、どのタブでも絞り込む
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        // 3. タブに応じて条件を追加
        if ($tab === 'mylist') {
            if (Auth::check()) {
                // Userモデルで定義した favoriteItems リレーションを利用
                $query->whereHas('favorites', function($q) {
                    $q->where('user_id', Auth::id());
                });
            } else {
                return redirect()->route('login'); // 未ログインならログインへ
            }
        } else {
            // おすすめ：自分が出品したものを除外
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
        }

        $items = $query->get();

        return view('index', compact('items', 'tab', 'keyword'));
    }

    public function show($item_id)
    {
        // 商品を取得。見つからなければ404エラーを出す
        $item = Item::with(['order', 'categories', 'comments.user'])->findOrFail($item_id);

        // 未認証ユーザーでも $item は取得できるので、そのままビューへ渡す
        return view('item', compact('item'));
    }

    //検索機能
    public function getSearch(Request $request)
    {
        $query = Item::query();

    if ($request->filled('keyword'))
        {
            $keyword = $request->input('keyword');
            $query->where('name','like','%'.$keyword.'%');
        }

        $items = $query->get();
        $tab = 'recommend';
        return view('index')->with(compact('items','tab'));
    }
}