<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class FavoriteController extends Controller
{
    public function toggle(Product $product)
    {
        $user = auth()->user();
    
        // 修正：カッコを正しく閉じ、セミコロンを削除する
        if ($product->isFavoritedBy($user)) {
            $product->favorites()->detach($user->id);
            $liked = false;
        } else {
            $product->favorites()->attach($user->id);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $product->favorites()->count(),
        ]);
    }
}
