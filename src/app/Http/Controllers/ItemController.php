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
        $keyword = $request->query('keyword');

        $query = Item::query();

        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        if ($tab === 'mylist') {
            if (Auth::check()) {
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

        $items = $query->paginate(14);

        return view('index', compact('items', 'tab', 'keyword'));
    }

    public function show($item_id)
    {
        $item = Item::with(['order', 'categories', 'comments.user'])->findOrFail($item_id);

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

        $items = $query->paginate(14);
        $tab = 'recommend';
        return view('index')->with(compact('items','tab'));
    }
}