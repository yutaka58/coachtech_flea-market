<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// --- 公開ルート（誰でもアクセス可能） ---
// トップページ（indexメソッド内でログイン判定を行っているため、authの外でOK）
Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'show']);



// ログイン・登録
Route::get('/login', [ItemController::class, 'getLogin'])->name('login');
Route::post('/login', [ItemController::class, 'login']);
Route::get('/register', [ItemController::class, 'getRegister']);
Route::post('/register', [ItemController::class, 'postRegister']);

// 検索機能
route::get('/search', [ItemController::class, 'getSearch']);

// --- 認証ルート（ログイン必須） ---
Route::middleware('auth')->group(function () {
    // ログアウト処理が必要ならここに追加
    Route::post('/logout', [ItemController::class, 'logout']);

    Route::get('/mypage/profile', [ItemController::class, 'editProfile']);
    Route::post('/mypage/profile', [ItemController::class, 'updateProfile']);

    Route::post('/products/{item_id}/favorite', [FavoriteController::class, 'toggle']);

    Route::get('/purchase/{item_id}', [ItemController::class, 'purchase']);
    Route::post('/purchase/{item_id}', [ItemController::class, 'storepurchase']);

    Route::post('/item/{item_id}/comment', [CommentController::class, 'store']);
});


