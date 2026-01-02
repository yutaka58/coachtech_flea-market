<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class FavoriteController extends Controller
{
    public function toggle($item_id)
    {
        $product = Product::findOrFail($item_id);
        $user = auth()->user();
    
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
