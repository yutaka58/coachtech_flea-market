<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // これがないと保存時にエラーになります
    protected $fillable = [
        'user_id',
        'item_id',
        'comment',
    ];

    public function user()
    {
        // 修正：e に直しました
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}