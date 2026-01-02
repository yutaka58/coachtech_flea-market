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

}
