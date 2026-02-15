<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'テストユーザー',
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'postal_code' => '100-0001',
            'address' => '東京都千代田区',
            'building' => 'テストビル',
            'email_verified_at' => now(),
        ];
    }

    public function verified()
    {
        return $this->state(fn () => [
            'email_verified_at' => now(),
        ]);
    }

    public function completedProfile()
    {
        return $this->state(fn () => [
            'name' => 'テストユーザー',
            'postal_code' => '100-0001',
            'address' => '東京都千代田区',
            'building' => 'テストビル',
        ]);
    }

}
