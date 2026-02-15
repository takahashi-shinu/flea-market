<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            if (app()->environment('testing')) {
                $event = json_decode($payload);
            } else {
                $event = Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $secret
                );
            }
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);

        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        \Log::info('Stripe webhook handled', [
            'event' => $event->type,
            'session_id' => $event->data->object->id,
        ]);

        switch ($event->type) {
            case 'checkout.session.completed':
                // クレカ用（即時確定）
                if ($event->data->object->payment_status === 'paid') {
                    $this->markAsPaid($event->data->object);
                }
                break;

            case 'checkout.session.async_payment_succeeded':
                // コンビニ払い完了
                $this->markAsPaid($event->data->object);
                break;

            case 'checkout.session.expired':
                $this->markAsExpired($event->data->object);
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    private function markAsPaid ($session)
    {
        DB::transaction(function () use ($session) {
            $purchaseId = data_get($session, 'metadata.purchase_id');

            $purchase = $purchaseId
                ? Purchase::where('id', $purchaseId)->lockForUpdate()->first()
                : Purchase::where('stripe_session_id', $session->id)->lockForUpdate()->first();

            if (!$purchase || $purchase->status !== Purchase::STATUS_PENDING) {
            return;
            }

            $item = Item::where('id', $purchase->item_id)
                ->lockForUpdate()
                ->first();

            if (!$item) {
                return;
            }

            $purchase->update([
            'status' => Purchase::STATUS_PAID,
            ]);

            $item->update([
                'status' => Item::STATUS_SOLD,
            ]);
        });
    }

    private function markAsExpired($session)
    {
        DB::transaction(function () use ($session) {

            $purchaseId = data_get($session, 'metadata.purchase_id');

            $purchase = $purchaseId
                ? Purchase::where('id', $purchaseId)->lockForUpdate()->first()
                : Purchase::where('stripe_session_id', $session->id)->lockForUpdate()->first();

            if (!$purchase || $purchase->status !== Purchase::STATUS_PENDING) {
                return;
            }

            $purchase->update([
                'status' => Purchase::STATUS_EXPIRED,
            ]);
        });
    }
}
