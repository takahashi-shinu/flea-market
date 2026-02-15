<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MypageTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインはログイン画面へ
    /** @test */
    public function guest_is_redirected_to_login()
    {
        $this->get(route('mypage'))
            ->assertRedirect(route('login'));
    }

    // プロフィール未完了はプロフィール編集へ
    /** @test */
    public function incomplete_profile_user_is_redirected()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => null,
        ]);

        $this->actingAs($user)
            ->get(route('mypage'))
            ->assertRedirect(route('profile.edit'));
    }

    // マイページが正常に表示される（デフォルト tab=sell）
    /** @test */
    public function mypage_is_displayed_with_sell_tab()
    {
        $user = User::factory()
            ->verified()
            ->completedProfile()
            ->create();

        // 出品商品
        $sellItem = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        // 購入商品
        $buyItem = Item::factory()->create();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
            'payment_method' => 'card',
            'postal_code' => '100-0001',
            'address' => '東京都千代田区',
            'status' => Purchase::STATUS_PAID,
        ]);

        $this->actingAs($user)
            ->get(route('mypage'))
            ->assertStatus(200)
            ->assertViewIs('users.mypage')
            ->assertViewHas('user', $user)
            ->assertViewHas('tab', 'sell')
            ->assertViewHas('sellItems', function ($items) use ($sellItem) {
                return $items->contains($sellItem);
            })
            ->assertViewHas('buyItems', function ($items) use ($buyItem) {
                return $items->contains($buyItem);
            });
    }

    // tab=buy の場合
    /** @test */
    public function mypage_buy_tab_is_displayed()
    {
        $user = User::factory()
            ->verified()
            ->completedProfile()
            ->create();

        $this->actingAs($user)
            ->get(route('mypage', ['tab' => 'buy']))
            ->assertStatus(200)
            ->assertViewHas('tab', 'buy');
    }
}