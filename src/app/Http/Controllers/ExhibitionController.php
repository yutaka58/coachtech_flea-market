<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;

use App\Http\Requests\ExhibitionRequest;

class ExhibitionController extends Controller
{
    // 出品画面を表示
    public function exhibition(Request $request)
    {
        $user = Auth::user();
        $categories = Category::all();

        return view('exhibition', compact('user', 'categories'));
    }

    public function storeItem(ExhibitionRequest $request)
    {
        $item = new Item();
        $item->user_id = Auth::id();
        $item->name = $request->name;
        $item->brand = $request->brand;
        $item->description = $request->description;
        $item->condition = $request->condition;

        $price = str_replace(['￥', '¥', ','], '', $request->price);
        $item->price = (int)$price;

        // 画像の保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $item->img_url = $path;
        } else {

            $item->img_url = 'items/default.png';
        }

        // 4. 商品テーブル(items)に保存
        $item->save();

        // 5. 中間テーブル(category_product)にカテゴリーを保存
        $item->categories()->attach($request->category_id);

        return redirect('/');
    }
}
