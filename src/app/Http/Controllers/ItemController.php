<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest; // これを使います
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Product;
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
        // RegisterRequest が自動で入力チェックをしてくれます
        
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
        return redirect('/mypage/profile')->with('success', '会員登録が完了しました。プロフィールを設定してください。');
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
        // 1. 現在のタブを取得（デフォルトは 'recommend'）
        $tab = $request->query('tab', 'recommend');

        // 現在ログインしているユーザーのIDを取得
        $userId = Auth::id();

        if ($tab === 'mylist') {
            // 2. マイリスト：ログイン中のユーザーがいいねした商品のみ取得
            // ※ Likeモデルや多対多のリレーションが必要です
            $products = Auth::check() 
                ? Auth::user()->favoriteItems 
                : collect(); // 未ログインなら空
        } else {
            // おすすめ：全商品から「自分が出品した商品」を除外
            $query = Product::with('order');

            if (Auth::check()) {
                // ログインしている場合、seller_id（出品者ID）が自分以外の商品を取得
                // ※カラム名が user_id の場合は書き換えてください
                $query->where('user_id', '!=', $userId);
            }

            $products = $query->get();
        }

        return view('index', compact('products', 'tab'));

    }

    public function show($product_id)
    {
        // 商品を取得。見つからなければ404エラーを出す
        $product = Product::with(['order'])->findOrFail($product_id);
    
        // 未認証ユーザーでも $product は取得できるので、そのままビューへ渡す
        return view('item', compact('product'));
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
        $query = Product::query();

    if ($request->filled('keyword'))
        {
            $keyword = $request->input('keyword');
            $query->where('name','like','%'.$keyword.'%');
        }

        $products = $query->get();
        $tab = 'recommend';
        return view('index')->with(compact('products','tab'));
    }

    public function purchase($item_id)
    {
        // 指定されたIDの商品データを取得
        $product = \App\Models\Product::findOrFail($item_id);

        // 購入画面（purchase.blade.php）を表示
        return view('purchase', compact('product'));
    }

}