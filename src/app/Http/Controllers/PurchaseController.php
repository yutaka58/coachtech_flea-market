<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Item;
use App\Models\User;
use App\Models\Order;

use App\Http\Requests\PurchaseRequest;

use Stripe\Stripe;
use Stripe\Checkout\Session;

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

        // セッションに保存された支払い方法を取得。なければ null
        $payment_id = session('payment_method');
        // ---------------------

        // 購入画面（purchase.blade.php）を表示
        return response()->view('purchase', compact('item', 'user', 'payment_id'));
    }

    // 購入実装
    public function storePurchase(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // 1. フォームから送られた配送先や支払い方法をセッションに一時保存
        session(['order_data' => [
            'item_id'        => $item_id,
            'payment_method' => $request->payment_method,
            'post_code'      => $request->post_code,
            'address'        => $request->address,
            'building'       => $request->building,
        ]]);

        // Stripe API設定
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // チェックアウトセッション作成
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => [$request->payment_method === 'card' ? 'card' : 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item_id' => $item->id]),
            'cancel_url'  => route('purchase.show', ['item_id' => $item->id]),
        ]);

        return redirect()->away($session->url);
    }

    // 決済完了後
    public function success($item_id)
    {
        // 2. セッションから保存しておいた注文情報を取り出す
        $orderData = session('order_data');

        // データが存在する場合のみDBに保存（リロード対策）
        if ($orderData && $orderData['item_id'] == $item_id) {
            \App\Models\Order::create([
                'user_id'        => auth()->id(),
                'item_id'        => $item_id,
                'payment_method' => $orderData['payment_method'],
                'post_code'      => $orderData['post_code'],
                'address'        => $orderData['address'],
                'building'       => $orderData['building'],
            ]);

        // 3. 保存が終わったらセッションを削除（二重保存防止）
        session()->forget('order_data');
        session()->forget('payment_method'); // 支払い方法選択のセッションもあれば削除
        }

        return redirect('/')->with('message', 'ご購入ありがとうございました！');
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
