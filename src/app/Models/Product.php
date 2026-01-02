<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'brand',
        'description',
        'img_url',
        'condition',
    ];

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function categories()
    {
        // 商品が複数のカテゴリーを持つ場合（多対多）
        return $this->belongsToMany(Category::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    //すでにいいねしているか判定するメソッド
    public function isFavoritedBy($user): bool
    {
        // $userがnull（未ログイン）なら即座にfalseを返す
        if (!$user) {
            return false;
        }

    // $userがオブジェクト（ログイン中）なら、そのIDで存在チェック
        return $this->favorites()->where('user_id', $user->id)->exists();
        }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

}
