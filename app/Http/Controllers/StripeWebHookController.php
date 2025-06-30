<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\SDCOptionController;
use App\Http\Controllers\NotificationController;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Admin\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class StripeWebHookController extends CashierWebhookController
{


    /**
     * Handle the incoming Stripe webhook
     */
    public function handleWebhook(Request $request)
    {
        $this->verifyWebhookSignature($request);

        $payload = json_decode($request->getContent(), true);
        $eventType = $payload['type'] ?? null;
        $object = $payload['data']['object'] ?? null;

        if (!$eventType || !$object) {
            return response()->json(['status' => 'invalid payload'], 400);
        }
        try {
            DB::beginTransaction();

            /* Handle different types of payment events */
            switch ($eventType) {
                /* For subscriptions - track all subscription activity */
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($payload);
                    break;
                /** Subscription Failed */
                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($payload);
                    break;

                /* For one-time payments */
                case 'payment_intent.succeeded':
                    /* Only handle payment intent events if they're NOT related to subscriptions */
                    if (empty($object['invoice'])) {
                        $this->handlePaymentIntentSucceeded($payload);
                    }
                    break;
                /** For One-Time Payment Failed */
                case 'payment_intent.payment_failed':
                    /* Only handle payment intent events if they're NOT related to subscriptions */
                     if (empty($object['invoice'])) {
                        $this->handlePaymentIntentFailed($payload);
                    }
                    break;

                default:
                    Log::info("Unhandled Stripe event: {$eventType}");
            }
            $response = parent::handleWebhook($request);
            DB::commit();
            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle subscription updated events
     * This triggers on new subscriptions and renewal payments
     */
    public function handleSubscriptionUpdated($payload)
    {
        $object = $payload['data']['object'];

        /* Only process subscription events (starting with 'sub_') */
        if (!isset($object['id']) || !str_starts_with($object['id'], 'sub_')) {
            return response()->json(['status' => 'ignored', 'reason' => 'not a subscription']);
        }

        /* Only process active subscriptions */
        if ($object['status'] !== 'active') {
            Log::info(
                'Skipping subscription that is not active',
                ['id' => $object['id'], 'status' => $object['status'] ?? 'unknown']
            );
            return response()->json(['status' => 'ignored', 'reason' => 'subscription not active']);
        }

        $metadata = $object['metadata'] ?? [];
        $userId = $this->getUserIdByCustomer($object['customer'] ?? null);

        /* For renewal detection - use current_period_start to identify billing cycles */
        $currentPeriodStart = $object['current_period_start'] ?? null;

        if (!$currentPeriodStart) {
            return response()->json(['status' => 'ignored', 'reason' => 'missing period information']);
        }

        /* Use subscription ID + period start as a unique identifier for this billing cycle */
        $stripeTransactionId = $object['id'];
        $periodStartDate = date('Y-m-d', $currentPeriodStart);

        /* Check for existing transaction with this subscription ID and period start date */
        $existing = Transaction::where('stripe_transaction_id', $stripeTransactionId)->where('created_at', $periodStartDate)->first();
        if (empty($existing)) {
            /* Get the plan information */
            $planAmount = 0;
            $planName = '';

            /* Handle different subscription structures */
            if (isset($object['plan'])) {
                /* Single plan subscription */
                $planAmount = $object['plan']['amount'] ?? 0;
                $planName = $object['plan']['nickname'] ?? $object['plan']['id'] ?? '';
            } elseif (isset($object['items']['data'][0]['plan'])) {
                /* Subscription with items */
                $planAmount = $object['items']['data'][0]['plan']['amount'] ?? 0;
                $planName = $object['items']['data'][0]['plan']['nickname'] ?? $object['items']['data'][0]['plan']['id'] ?? '';
            }
            /* Create transaction record */
            $transaction = Transaction::create([
                'user_id' => $userId,
                'stripe_customer_id' => $object['customer'] ?? null,
                'stripe_transaction_id' => $stripeTransactionId,
                'subscription_id' => $object['id'],
                'amount' => $this->formatAmount($planAmount),
                'currency' => strtoupper($object['currency'] ?? 'USD'),
                'payment_method' => $this->extractPaymentMethod($object, $payload),
                'description' =>  $object['description'] ?? 'Subscription payment',
                'status' => 'paid',
                'payment_for' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $device = Device::select('id', 'device_title', 'service_provider_id', 'subscription_id', 'org_user_full_name', 'device_owner_name')->where('subscription_id', $object['id'])->where('active', 1)->first();
            if ($device) {
                $userName = $device->org_user_full_name ?? $device->device_owner_name;
                $message = "$userName has renewed the subscription for device: {$device->device_title}.";

                $notificationRequest = [
                    'user_id' => $device->user_id,
                    'message' => $message,
                    'notification_for' => 'subscription',
                    'device_id' => $device->id,
                    'org_id' => null,
                    'sub_org_id' => null,
                    'user_target_link' => 'sdcsmuser/device-list',
                    'admin_target_link' => 'smarttiusadmin/devices',
                    'service_provider_id' => $device->service_provider_id,
                ];
                NotificationController::addNotification($notificationRequest);
                $expiration = isset($object['current_period_end']) ? date('Y-m-d', $object['current_period_end']) : null;
                $startDate = isset($object['current_period_start']) ? date('Y-m-d', $object['current_period_start']) : null;
                $device->update(['expiration_date' => $expiration, 'payment_added_date' => $startDate]);
                /* Update the transaction we just created with the device info */
                $transaction->device_id = $device->id;
                $transaction->service_provider_id = $device->service_provider_id ?? 1;
                $transaction->save();
            }
        } else {
            Log::info('Skipped duplicate subscription transaction', [
                'stripe_transaction_id' => $stripeTransactionId,
                'period_start' => $periodStartDate
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    /** Handle Subscription failed */
    public function handleInvoicePaymentFailed($payload)
    {
        $object = $payload['data']['object'];
        /** Ignoring failed invoice not linked to subscription. */
        if (!isset($object['subscription'])) {
            return response()->json(['status' => 'ignored', 'reason' => 'no subscription']);
        }

        $subscriptionId = $object['subscription'];
        $stripeTransactionId = $object['id']; /* invoice ID */
        $userId = $this->getUserIdByCustomer($object['customer'] ?? null);

        $metadata = $object['lines']['data'][0]['metadata'] ?? [];
        $amount = $object['amount_due'] ?? 0;
        $currency = strtoupper($object['currency'] ?? 'USD');
        $paymentStatus = $object['status'] ?? 'failed';

        $existing = Transaction::where('stripe_transaction_id', $stripeTransactionId)->first();
        if (empty($existing)) {
            $transaction = Transaction::create([
                'user_id' => $userId,
                'stripe_customer_id' => $object['customer'] ?? null,
                'stripe_transaction_id' => $stripeTransactionId,
                'subscription_id' => $subscriptionId,
                'amount' => $this->formatAmount($amount),
                'currency' => $currency,
                'payment_method' => 'card',
                'description' => $object['description'] ?? 'Failed subscription payment',
                'status' => $paymentStatus,
                'payment_for' => 1,
                'service_provider_id' => (int) ($metadata['service_provider_id'] ?? 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::warning('Recorded failed subscription payment', [
                'transaction_id' => $transaction->id,
                'stripe_transaction_id' => $stripeTransactionId,
                'status' => $paymentStatus
            ]);
        } else {
            Log::info('Duplicate failed subscription transaction skipped', ['stripe_transaction_id' => $stripeTransactionId]);
        }

        return response()->json(['status' => 'recorded_failed_subscription_payment']);
    }


    /**
     * Handle one-time payments
     */
    public function handlePaymentIntentSucceeded($payload)
    {
        $object = $payload['data']['object'];

        /* Only process payment intent events (starting with 'pi_') */
        if (!isset($object['id']) || !str_starts_with($object['id'], 'pi_')) {
            Log::info('Ignoring non-payment-intent transaction', ['id' => $object['id'] ?? 'unknown']);
            return response()->json(['status' => 'ignored', 'reason' => 'not a payment intent transaction']);
        }

        /* Skip if this payment is related to an invoice (would be handled by subscription events) */
        if (!empty($object['invoice'])) {
            return response()->json(['status' => 'ignored', 'reason' => 'payment for invoice']);
        }

        $metadata = $object['metadata'] ?? [];
        $userId = $this->getUserIdByCustomer($object['customer'] ?? null);

        /* Use payment intent ID as the transaction ID */
        $stripeTransactionId = $object['id']; // This will be the 'pi_' prefixed ID

        /* Check for existing transaction with this payment intent ID to prevent duplicates */
        $existing = Transaction::where('stripe_transaction_id', $stripeTransactionId)->first();
        if (empty($existing)) {
            $transaction = Transaction::create([
                'user_id' => $userId,
                'stripe_customer_id' => $object['customer'] ?? null,
                'stripe_transaction_id' => $stripeTransactionId,
                'subscription_id' => null,
                'amount' => $this->formatAmount($object['amount'] ?? 0),
                'currency' => strtoupper($object['currency'] ?? 'USD'),
                'payment_method' => $this->extractPaymentMethod($object, $payload),
                'description' => $object['description'] ?? 'One-time payment',
                'status' => $object['status'],
                'payment_for' => isset($metadata['payment_for']) ? (int)$metadata['payment_for'] : 1,
                'service_provider_id' => (int) ($metadata['service_provider_id'] ?? 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            Log::info('Skipped duplicate transaction', ['stripe_transaction_id' => $stripeTransactionId]);
        }

        return response()->json(['status' => 'success']);
    }

    /** One time Payment */
    public function handleChargeSucceeded($payload)
    {
        $object = $payload['data']['object'];
        $metadata = $object['metadata'] ?? [];
        $userId = $this->getUserIdByCustomer($object['customer'] ?? null);
        $existing = Transaction::where('stripe_transaction_id', $object['id'])->first();
        if (empty($existing)) {
            Transaction::create([
                'user_id' => $userId,
                'stripe_customer_id' => $object['customer'] ?? null,
                'stripe_transaction_id' => $object['id'] ?? null,
                'subscription_id' => null,
                'amount' => $this->formatAmount($object['amount'] ?? 0),
                'currency' => strtoupper($object['currency'] ?? 'USD'),
                'payment_method' => $this->extractPaymentMethod($object, $payload),
                'description' => $object['description'],
                'status' => $object['status'],
                'payment_for' => (int) ($metadata['payment_for'] ?? 1),
                'service_provider_id' => (int) ($metadata['service_provider_id'] ?? 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /** On time Payment Failed */
    public function handlePaymentIntentFailed($payload)
    {
        $object = $payload['data']['object'];
        /** Ignoring non-payment-intent failure. */
        if (!isset($object['id']) || !str_starts_with($object['id'], 'pi_')) {
            return response()->json(['status' => 'ignored', 'reason' => 'not a payment intent']);
        }
        if (!empty($object['invoice'])) {
            return response()->json(['status' => 'ignored', 'reason' => 'subscription invoice']);
        }

        $metadata = $object['metadata'] ?? [];
        $userId = $this->getUserIdByCustomer($object['customer'] ?? null);
        $stripeTransactionId = $object['id'];

        $existing = Transaction::where('stripe_transaction_id', $stripeTransactionId)->first();

        if (empty($existing)) {
            $transaction = Transaction::create([
                'user_id' => $userId,
                'stripe_customer_id' => $object['customer'] ?? null,
                'stripe_transaction_id' => $stripeTransactionId,
                'subscription_id' => null,
                'amount' => $this->formatAmount($object['amount'] ?? 0),
                'currency' => strtoupper($object['currency'] ?? 'USD'),
                'payment_method' => $this->extractPaymentMethod($object, $payload),
                'description' => $object['description'] ?? 'Failed one-time payment',
                'status' => $object['status'] == 'requires_payment_method' ? 'failed' : $object['status'],
                'payment_for' => isset($metadata['payment_for']) ? (int)$metadata['payment_for'] : 1,
                'service_provider_id' => (int) ($metadata['service_provider_id'] ?? 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            Log::info('Duplicate failed transaction skipped', ['stripe_transaction_id' => $stripeTransactionId]);
        }

        return response()->json(['status' => 'recorded_failed_intent']);
    }



    /**
     * Format the amount by converting from cents to dollars/euros
     */
    protected function formatAmount($amount): float
    {
        return bcdiv($amount, 100, 2);
    }



    /**
     * Extract payment method from various object structures
     */
    protected function extractPaymentMethod(array $object, array $payload): ?string
    {
        /* Try multiple possible locations */
        if (!empty($object['payment_method_types']) && is_array($object['payment_method_types'])) {
            return $object['payment_method_types'][0];
        }

        if (!empty($object['payment_method']) && is_string($object['payment_method'])) {
            return $object['payment_method'];
        }

        if (!empty($object['payment_method_details']['type'])) {
            return $object['payment_method_details']['type'];
        }

        if (!empty($object['default_payment_method'])) {
            return $object['default_payment_method'];
        }

        /* For invoices, might need to check lines for payment method */
        if (isset($object['lines']) && is_array($object['lines']) && !empty($object['lines']['data'])) {
            foreach ($object['lines']['data'] as $line) {
                if (!empty($line['payment_method_details']['type'])) {
                    return $line['payment_method_details']['type'];
                }
            }
        }

        return 'card';
    }



    /**
     * Get a user ID from a Stripe customer ID
     */
    protected function getUserIdByCustomer(?string $stripeCustomerId): ?int
    {
        if (!$stripeCustomerId) {
            return null;
        }
        $userId = User::where('stripe_id', $stripeCustomerId)->value('id');
        return $userId;
    }

    /**
     * Get the webhook secret from options
     */
    protected function getWebhookSecret(): string
    {
        $secret = SDCOptionController::getOption('sdcsm_stripe_webhook_secret', 1);
        /** Stripe webhook secret not configured in options table. */
        if (empty($secret)) {
            throw new \RuntimeException('Webhook secret not configured');
        }
        return $secret;
    }

    /**
     * Verify the webhook signature
     */
    protected function verifyWebhookSignature(Request $request): void
    {
        try {
            Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $this->getWebhookSecret()
            );
        } catch (\UnexpectedValueException $e) {
            throw new AccessDeniedHttpException('Invalid Stripe payload.');
        } catch (SignatureVerificationException $e) {
            throw new AccessDeniedHttpException('Invalid Stripe signature.');
        }
    }
}
