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
        
        // 支払い方法の取得とバリデーション
        $method = $request->payment_method ?: session('payment_method');
        if (!$method) {
            return redirect()->back()->withErrors(['payment_method' => '支払い方法を選択してください。']);
        }

        // --- 注文情報の保存をここで行う場合 ---
        // 注意：本来はStripeの決済が完了した通知（Webhook）を受けてから保存するのが理想ですが、
        // 簡易実装として決済画面へ飛ぶ直前に作成します。
        Order::create([
            'user_id'        => auth()->id(),
            'item_id'        => $item_id,
            'payment_method' => $method,
            'post_code'      => $request->post_code,
            'address'        => $request->address,
            'building'       => $request->building,
        ]);

        // Stripe APIキーの設定
        Stripe::setApiKey(config('services.stripe.secret') ?: env('STRIPE_SECRET_KEY'));

        // Stripe Checkoutセッションの作成
        $session = Session::create([
            'payment_method_types' => [$method === 'card' ? 'card' : 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item_id' => $item->id]),
            'cancel_url'  => route('purchase.show', ['item_id' => $item->id]),
        ]);

        // 購入完了処理に向けてセッションを掃除
        session()->forget('payment_method');

        return redirect()->away($session->url);
    }

    public function success($item_id)
    {
        // ここでメッセージを添えてトップページへリダイレクト
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
