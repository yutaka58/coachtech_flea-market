<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
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
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 売り切れ判定
        $isSoldOut = Order::where('item_id', $item_id)->exists();
        if ($isSoldOut) {
            return redirect('/');
        }

        // --- 💡 ここを修正 ---
        // セッションに保存された支払い方法を取得。なければ null
        $payment_id = session('payment_method');
        // ---------------------

        // 購入画面（purchase.blade.php）を表示
        return response()->view('purchase', compact('item', 'user', 'payment_id'));
    }

    // 購入実装
    public function storepurchase(PurchaseRequest $request, $item_id)
    {
        // セッションから保存しておいた支払い方法を取得
        $paymentMethod = $request->payment_method ?: session('payment_method');

        if (!$paymentMethod) {
            return redirect()->back()->with('error', '支払い方法を選択してください。');
        }

        \App\Models\Order::create([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
            'payment_method' => $paymentMethod, // DBのカラム名に合わせてください
        ]);

        // 購入完了後はセッションを削除する
        session()->forget('payment_method');

        return redirect('/')->with('success', '購入が完了しました！');
    }

    public function savePaymentMethod(Request $request, $item_id)
    {
        // セッションに保存
        session(['payment_method' => $request->payment_method]);

        // AJAX(fetch)リクエストの場合はJSONを返す
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        // 普通のリクエストの場合は購入画面へ戻る
       return redirect()->route('purchase.show', ['item_id' => $item_id]);
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

    // 住所変更画面を表示
    public function address(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('address', compact('item'));
    }

    // 住所変更の更新
    public function updateaddress(AddressRequest $request, $item_id)
    {
        // ...住所を保存する処理（既存のコード）...
        $user = Auth::user();
        $user->post_code = $request->post_code;
        $user->address = $request->address;
        $user->building = $request->building;
        $user->save();

        // 💡 重要：JSONを返すのではなく、購入画面へリダイレクトさせる
        // これにより {"success": true} の画面には行かず、元の購入ページに戻ります
        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    // 出品画面を表示
    public function exhibition(Request $request)
    {
        $user = Auth::user();
        $categories = Category::all();

        if ($request->hasFile('image')) {

            // 古い画像があれば削除
            if ($user->image) {
                Storage::disk('public')->delete('$user->image');
            }
            // storage/app/public/profiles に保存される
            $path = $request->file('image')->store('profiles', 'public');
            $user->image = $path;
        }

        return view('exhibition', compact('user', 'categories'));
    }

}