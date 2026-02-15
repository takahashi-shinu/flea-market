<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインユーザーは住所変更画面にアクセスできない
    /** @test */
    public function guest_cannot_access_address_edit_page()
    {
        $item = Item::factory()->create();

        $response = $this->get(route('purchase.address.edit', $item));

        $response->assertRedirect(route('login'));
    }

    // ログインユーザーは住所変更画面を表示できる
    /** @test */
    public function authenticated_user_can_view_address_edit_page()
    {
        $user = User::factory()->verified()->completedProfile()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('purchase.address.edit', $item));

        $response->assertStatus(200);
        $response->assertViewIs('items.newaddress');
    }

    // 配送先住所をセッションに保存できる
    /** @test */
    public function shipping_address_is_saved_into_session()
    {
        $user = User::factory()->verified()->completedProfile()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('purchase.address.update', $item), [
                'postal_code' => '123-4567',
                'address'     => '東京都渋谷区1-1-1',
                'building'    => 'テストビル101',
            ]);

        $response->assertSessionHas('shipping_postal_code', '123-4567');
    }

    // 住所更新後は購入画面へリダイレクトされる
    /** @test */
    public function after_updating_address_user_is_redirected_to_purchase_page()
    {
        $user = User::factory()->verified()->completedProfile()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('purchase.address.update', $item), [
                'postal_code' => '123-4567',
                'address'     => '東京都渋谷区1-1-1',
                'building'    => '',
            ]);

        $response->assertRedirect(route('purchase.create', $item));
    }

    // 郵便番号が未入力だとエラーになる
    /** @test */
    public function postal_code_is_required()
    {
        $user = User::factory()->verified()->completedProfile()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('purchase.address.update', $item), [
                'postal_code' => '',
                'address' => '',
            ]);

        $response->assertSessionHasErrors(['postal_code', 'address']);
    }
}