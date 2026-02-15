<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    // 購入画面が表示される
    /** @test */
    public function purchase_page_is_displayed()
    {
        $user = User::factory()
            ->verified()
            ->completedProfile()
            ->create();

        $item = Item::factory()->create([
            'status' => Item::STATUS_SELLING,
        ]);

        $this->actingAs($user)
            ->get(route('purchase.create', $item))
            ->assertStatus(200)
            ->assertViewIs('items.purchase')
            ->assertViewHas('item', $item);
    }

    // Stripe決済ページへリダイレクトされる
    /** @test */
    public function redirect_to_stripe_checkout()
    {
        $user = User::factory()
            ->verified()
            ->completedProfile()
            ->create();

        $item = Item::factory()->create([
            'status' => Item::STATUS_SELLING,
        ]);

        $response = $this->actingAs($user)
            ->post(route('purchase.stripe', $item), [
                'payment_method' => 'card',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'status' => Purchase::STATUS_PENDING,
        ]);

        $this->assertDatabaseMissing('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'status' => Purchase::STATUS_PAID,
        ]);

        $purchase = Purchase::first();
        $this->assertNotNull($purchase->stripe_session_id);
    }

    // successで購入後商品一覧画面に遷移する
    /** @test */
    public function purchase_success_when_paid()
    {
        $user = User::factory()->verified()->completedProfile()->create();

        $item = Item::factory()->create();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'status' => Purchase::STATUS_PAID,
            'stripe_session_id' => 'test_session',
            'postal_code' => '123-4567',
            'address' => 'test',
            'building' => null,
        ]);

        $this->actingAs($user)
            ->get(route('purchase.success', ['session_id' => 'test_session']))
            ->assertRedirect(route('items.index'))
            ->assertSessionHas('success');
    }


    // 売り切れ商品は購入画面に入れない
    /** @test */
    public function sold_item_cannot_access_purchase_page()
    {
        $user = User::factory()
            ->verified()
            ->completedProfile()
            ->create();

        $item = Item::factory()->create([
            'status' => Item::STATUS_SOLD,
        ]);

        $this->actingAs($user)
            ->get(route('purchase.create', $item))
            ->assertRedirect(route('items.show', $item));
    }
}
