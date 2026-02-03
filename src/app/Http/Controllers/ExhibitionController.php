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
        $item->description = $request->description;
        $item->condition = $request->condition;

        // 価格から記号を除去して数値に変換
        $price = str_replace(['￥', '¥', ','], '', $request->price);
        $item->price = (int)$price;

        // 画像の保存（カラム名はデータベースに合わせ img_url）
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $item->img_url = $path;
        } else {
            // 画像がない場合に備えて、デフォルトのパスを入れる（エラー回避用）
            $item->img_url = 'items/default.png';
        }

        // 4. 商品テーブル(items)に保存
        $item->save();

        // 5. 中間テーブル(category_product)にカテゴリーを保存
        // attachを使うことで、多対多のリレーションが保存されます
        $item->categories()->attach($request->category_id);

        return redirect('/')->with('success', '出品が完了しました');
    }
}
