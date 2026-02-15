<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Stripe\WebhookSignature;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function checkout_session_completed_marks_purchase_as_paid()
    {
        $item = Item::factory()->create([
            'status' => Item::STATUS_AVAILABLE,
        ]);

        $purchase = Purchase::factory()->create([
            'item_id' => $item->id,
            'stripe_session_id' => 'cs_test_123',
            'status' => Purchase::STATUS_PENDING,
        ]);

        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'payment_status' => 'paid',
                ],
            ],
        ]);

        $this->postJson(route('stripe.webhook'), json_decode($payload, true))->assertOk();

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => Purchase::STATUS_PAID,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => Item::STATUS_SOLD,
        ]);
    }

    /** @test */
    public function async_payment_succeeded_marks_purchase_as_paid()
    {
        $item = Item::factory()->create([
            'status' => Item::STATUS_SELLING,
        ]);

        $purchase = Purchase::factory()->create([
            'item_id' => $item->id,
            'stripe_session_id' => 'cs_test_konbini',
            'status' => Purchase::STATUS_PENDING,
        ]);

        $payload = json_encode([
            'type' => 'checkout.session.async_payment_succeeded',
            'data' => [
                'object' => [
                    'id' => 'cs_test_konbini',
                ],
            ],
        ]);

        $this->postJson(route('stripe.webhook'), json_decode($payload, true))->assertOk();

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => Purchase::STATUS_PAID,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => Item::STATUS_SOLD,
        ]);
    }

    /** @test */
    public function webhook_is_idempotent()
    {
        $item = Item::factory()->create([
            'status' => Item::STATUS_SELLING,
        ]);

        $purchase = Purchase::factory()->create([
            'item_id' => $item->id,
            'stripe_session_id' => 'cs_test_123',
            'status' => Purchase::STATUS_PENDING,
        ]);

        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'payment_status' => 'paid',
                ],
            ],
        ]);

        // 2回投げる
        $this->postJson(route('stripe.webhook'), json_decode($payload, true));
        $this->postJson(route('stripe.webhook'), json_decode($payload, true));

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => Purchase::STATUS_PAID,
        ]);
    }
}

