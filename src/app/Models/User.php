<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'email',
        'password',
        'postal_code',
        'address',
        'building',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // メール認証済みかの判断
    public function isProfileCompleted(): bool
    {
        return
            !empty($this->name) &&
            !empty($this->postal_code) &&
            !empty($this->address);
    }

    // リレーション
    // 出品した商品
    public function items()
    {
        return $this->hasMany(Item::class);
    }
    // いいね（中間テーブル）
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    // いいねした商品一覧
    public function favoriteItems()
    {
        return $this->belongsToMany(Item::class, 'favorites');
    }
    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    // 購入履歴
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    // 購入した商品
    public function purchasedItems()
    {
    return $this->belongsToMany(Item::class, 'purchases', 'user_id', 'item_id')
        ->withPivot('payment_method', 'postal_code', 'address', 'building') // 購入時の情報を取得
        ->withTimestamps();
    }
}
