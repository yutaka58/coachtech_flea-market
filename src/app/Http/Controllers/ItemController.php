<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest; // これを使います
use App\Http\Requests\LoginRequest;
use App\Models\User;
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

        if ($tab === 'mylist') {
        // 2. マイリスト：ログイン中のユーザーがいいねした商品のみ取得
        // ※ Likeモデルや多対多のリレーションが必要です
        $items = Auth::check() 
            ? Auth::user()->favoriteItems 
            : collect(); // 未ログインなら空
    } else {
        // 3. おすすめ：全商品（または特定のロジックで抽出）を表示
        $items = Item::all();
    }

        return view('index', compact('items', 'tab'));
    }

}