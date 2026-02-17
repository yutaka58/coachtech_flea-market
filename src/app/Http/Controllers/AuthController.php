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
    // ログイン処理
    public function login(LoginRequest $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 1.メール認証が済んでいない場合
            if (!$user->hasVerifiedEmail()) {

                return redirect()->route('verification.notice');
            }

            // 2.メール認証済み、かつプロフィールが未設定（初回）の場合はプロフィール設定へ
            if (empty($user->address)) {
                return redirect('/mypage/profile');
            }

            // 3.それ以外（2回目以降）は商品一覧（トップページ）へ
            return redirect('/');
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }


    // 会員登録の保存処理
    public function Register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 認証メールを送信
        event(new \Illuminate\Auth\Events\Registered($user));

        // 認証誘導画面へリダイレクト
        return redirect()->route('verification.notice');
    }

    public function certification(Request $request)
    {
        return view('auth.certification');
    }

        //ログアウト機能
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
