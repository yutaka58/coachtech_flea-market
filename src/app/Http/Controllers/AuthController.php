<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ログイン画面表示
    public function getLogin() {
        return view('auth.login');
    }

    // 会員登録画面表示
    public function getRegister() {
        return view('auth.register');
    }

    // 会員登録の保存処理
    public function postRegister(RegisterRequest $request)
    {
        // 1. ユーザー作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2. 作成したユーザーで自動ログインさせる
        Auth::login($user);

        // 3. プロフィール設定画面へリダイレクト
        return redirect('/mypage/profile');
    }

    // ログイン処理
    public function login(LoginRequest $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
        return redirect('/');
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }

        //ログアウト機能
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate(); // セッションを破棄
        $request->session()->regenerateToken(); // CSRFトークンを再生成

        return redirect('/login'); // ログイン画面へ遷移
    }
}
