<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class FavoriteController extends Controller
{
    public function toggle($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();
    
        if ($item->isFavoritedBy($user)) {
            $item->favorites()->detach($user->id);
            $liked = false;
        } else {
            $item->favorites()->attach($user->id);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $item->favorites()->count(),
        ]);
    }
}
