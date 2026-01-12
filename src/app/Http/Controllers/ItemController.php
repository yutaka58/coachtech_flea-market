<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RegisterRequest; // これを使います
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{

    // ログイン画面表示
    public function getLogin() {
        return view('auth.login');
    }

    // 会員登録画面表示
    public function getRegister() {
        return view('auth.register'); // 画面を出すだけ
    }

    // ★重要：会員登録の保存処理
    public function postRegister(RegisterRequest $request) 
    {
        // ユーザー作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2. 作成したユーザーで自動ログインさせる
        // これにより、次の画面が「認証が必要なページ」でもアクセス可能になります
        Auth::login($user);

        // 3. プロフィール設定画面へリダイレクト
        // /mypage/profile へのルートが web.php にあることを確認してください
        return redirect('/mypage/profile');
    }

    // ログイン処理（簡易版）
    public function login(LoginRequest $request) {
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
        return redirect('/');
    }

        // 認証失敗時に「login_error」という名前でメッセージを返す
        return back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }

    // プロフィール設定画面を表示する
    public function editProfile() {
        // 現在ログインしているユーザー情報を取得
        $user = Auth::user();
        return view('profile', compact('user')); // profile.blade.phpを表示
    }

    // app/Http/Controllers/ItemController.php

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
        $item = Item::with(['order'])->findOrFail($item_id);
    
        // 未認証ユーザーでも $item は取得できるので、そのままビューへ渡す
        return view('item', compact('item'));
    }

    //ログアウト機能
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate(); // セッションを破棄
        $request->session()->regenerateToken(); // CSRFトークンを再生成
    
        return redirect('/login'); // ログイン画面へ遷移
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

    // 購入画面を表示用
    public function purchase($item_id)
    {
        // 指定されたIDの商品データを取得
        $item = \App\Models\Item::findOrFail($item_id);

        $user = Auth::user();

        // 初期値として null や空文字を設定して渡す
        $payment_id = null;

        // 購入画面（purchase.blade.php）を表示
        return view('purchase', compact('item', 'user', 'payment_id'));
    }

    // 購入実装
    public function storepurchase(request $request, $item_id)
    {
        Order::create([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
        ]);

        return redirect('/');
    }

    // マイページ表示用
    public function mypage(Request $request)
    {
        $user = Auth::user();
        // 現在のタブを取得
        $tab = $request->query('tab', 'sell');
        // 出品した商品を取得
        $sellItems = Item::where('user_id', $user->id)->get();
        // 購入した商品を取得(Orderモデル経由でItemを取得)
        $buyItems = Item::whereHas('order', function($q) use($user) {
            $q->where('user_id', $user->id);
        })->get();

        return view('mypage', compact('user', 'tab', 'sellItems', 'buyItems'));
    }

    // プロフィール変更用
    public function updateProfile(Request $request)
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

    // 住所変更画面を表示
    public function address(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('address', compact('item'));
    }

    // 住所変更の更新
    public function updateaddress(Request $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        $user->post_code = $request->post_code;
        $user->address = $request->address;
        $user->building = $request->building;
        $user->save();

        Auth::setUser($user);
        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

}