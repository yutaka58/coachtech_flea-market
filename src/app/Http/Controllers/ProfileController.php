<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Item;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;


class ProfileController extends Controller
{
    // プロフィール設定画面を表示する
    public function editProfile() {
        // 現在ログインしているユーザー情報を取得
        $user = Auth::user();
        return view('profile', compact('user')); // profile.blade.phpを表示
    }

    // プロフィール変更用
    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();

        if ($request->hasFile('image')) {

            // 古い画像があれば削除
            if ($user->image) {
                Storage::disk('public')->delete('$user->image');
            }
            // storage/app/public/profiles に保存される
            $path = $request->file('image')->store('profiles', 'public');
            $user->image = $path;
        }

        $user->name = $request->name;
        $user->post_code = $request->post_code;
        $user->address = $request->address;
        $user->building = $request->building;

        $user->save();

        return redirect('/');
    }


    // マイページ表示用
    public function mypage(Request $request)
    {
        $user = Auth::user();
        // 現在のタブを取得
        $page = $request->query('page', 'sell');
        // 出品した商品を取得
        $sellItems = Item::where('user_id', $user->id)->get();
        // 購入した商品を取得(Orderモデル経由でItemを取得)
        $buyItems = Item::whereHas('order', function($q) use($user) {
            $q->where('user_id', $user->id);
        })->get();

        return view('mypage', compact('user', 'page', 'sellItems', 'buyItems'));
    }


    // 住所変更画面を表示
    public function address(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('address', compact('item'));
    }

    // 住所変更の更新
    public function updateaddress(AddressRequest $request, $item_id)
    {
        $user = Auth::user();
        $user->post_code = $request->post_code;
        $user->address = $request->address;
        $user->building = $request->building;
        $user->save();

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }
}
