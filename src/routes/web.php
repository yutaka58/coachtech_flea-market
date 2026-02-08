<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'Register']);

// --- メール認証誘導・処理（authのみ必要、verifiedは不要） ---
Route::middleware('auth')->group(function () {
    // b. メール認証誘導画面
    Route::get('/email/verify', [AuthController::class, 'certification'])->name('verification.notice');

    // c. メール認証処理（メール内リンククリック時）
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/mypage/profile'); // d. プロフィール設定画面へ
    })->middleware('signed')->name('verification.verify');

    // 認証メール再送
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送しました。');
    })->middleware('throttle:6,1')->name('verification.send');
    
    // ログアウトは認証さえしてればいつでもできるように外に出しておく
    Route::post('/logout', [AuthController::class, 'logout']);
});

// 検索機能
route::get('/search', [ItemController::class, 'getSearch']);


// --- 認証ルート（ログイン必須） ---
Route::middleware('auth', 'verified')->group(function () {
    // ログアウト
    Route::post('/logout', [AuthController::class, 'logout']);

    // プロフィール
    Route::get('/mypage/profile', [ProfileController::class, 'editProfile']);
    Route::post('/mypage/profile', [ProfileController::class, 'updateProfile']);

    // いいね
    Route::post('/products/{item_id}/favorite', [FavoriteController::class, 'toggle']);
    // コメント
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store']);

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'mypage']);

    // 購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'storePurchase'])->name('purchase.store');
    Route::post('/purchase/payment/{item_id}', [PurchaseController::class, 'savePaymentMethod'])->name('payment.save_session');

    // 決済成功時のページ
    Route::get('purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');

    // 住所変更
    Route::get('/purchase/address/{item_id}', [ProfileController::class, 'address'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [ProfileController::class, 'updateAddress'])->name('address.update');


    // 出品
    Route::get('/sell', [ExhibitionController::class, 'exhibition']);
    Route::post('/sell', [ExhibitionController::class, 'storeItem']);
});


