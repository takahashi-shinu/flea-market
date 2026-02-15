<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => 'テスト商品',
            'image' => 'test.jpg',
            'brand_name' => 'テストブランド',
            'condition' => '新品',
            'price' => 1000,
            'description' => 'テスト用の商品です',
            'status' => Item::STATUS_SELLING,
        ];
    }

    public function sold()
    {
        return $this->state([
            'status' => Item::STATUS_SOLD,
        ]);
    }
}