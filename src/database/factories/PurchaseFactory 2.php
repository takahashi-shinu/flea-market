<?php

namespace Database\Factories;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'item_id' => \App\Models\Item::factory(),
            'payment_method' => 'card',
            'postal_code' => '100-0001',
            'address' => '東京都',
            'building' => 'テスト',
            'status' => Purchase::STATUS_PENDING,
            'stripe_session_id' => 'cs_test_' . $this->faker->uuid,
        ];
    }

    public function paid()
    {
        return $this->state([
            'status' => Purchase::STATUS_PAID,
        ]);
    }

    public function convenience()
    {
        return $this->state([
            'payment_method' => 'convenience',
        ]);
    }
}
