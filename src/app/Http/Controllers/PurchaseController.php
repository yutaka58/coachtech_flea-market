<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Item;
use App\Models\User;
use App\Models\Order;

use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
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
}
