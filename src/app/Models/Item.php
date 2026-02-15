<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    const STATUS_SELLING = 'selling';
    const STATUS_SOLD = 'sold';
    const STATUS_AVAILABLE = 'available';

    protected $fillable = [
        'user_id',
        'name',
        'image',
        'brand_name',
        'condition',
        'price',
        'description',
        'status',
    ];

    // リレーション
    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリ（複数）
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // いいね
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }


    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 購入情報
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    // スコープ用
    // 販売中のみ
    public function scopeSelling($query)
    {
        return $query->where('status', self::STATUS_SELLING);
    }

    // 販売済みのみ
    public function scopeSold($query)
    {
        return $query->where('status', self::STATUS_SOLD);
    }

    // 判定用メソッド
    // 販売中か？
    public function isSelling()
    {
        return $this->status === self::STATUS_SELLING;
    }
    // 販売済みか？
    public function isSold()
    {
        return $this->status === self::STATUS_SOLD;
    }
}
