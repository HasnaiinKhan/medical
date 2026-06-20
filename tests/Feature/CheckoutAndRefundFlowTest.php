<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Refund;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CheckoutAndRefundFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('razorpay.key_id', 'rzp_test_key');
        Config::set('razorpay.key_secret', 'rzp_test_secret');
        Config::set('razorpay.webhook_secret', 'rzp_test_webhook_secret');
    }

    public function test_payment_verification_rejects_mismatched_razorpay_order_id(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'AHM-TEST001',
            'customer_name' => 'Test User',
            'customer_phone' => '9876543210',
            'delivery_pin' => '380001',
            'delivery_area' => 'Ahmedabad',
            'address_line1' => 'Line 1',
            'address_line2' => null,
            'subtotal_paise' => 10000,
            'delivery_fee_paise' => 0,
            'total_paise' => 10000,
            'payment_method' => 'online',
            'payment_status' => 'pending',
            'razorpay_order_id' => 'order_expected_123',
            'status' => 'placed',
        ]);

        $response = $this->actingAs($user)->postJson(route('checkout.verify'), [
            'razorpay_order_id' => 'order_other_456',
            'razorpay_payment_id' => 'pay_123',
            'razorpay_signature' => 'sig_123',
            'order_id' => $order->id,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'ok' => false,
                'message' => 'Payment verification failed for this order. Please try again from checkout.',
            ]);

        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertSame('placed', $order->fresh()->status);
    }

    public function test_payment_verification_uses_matching_razorpay_order_for_same_user_when_client_posts_stale_order_id(): void
    {
        $user = User::factory()->create();

        $staleOrder = Order::create([
            'user_id' => $user->id,
            'order_number' => 'AHM-STALE001',
            'customer_name' => 'Test User',
            'customer_phone' => '9876543210',
            'delivery_pin' => '380001',
            'delivery_area' => 'Ahmedabad',
            'address_line1' => 'Line 1',
            'address_line2' => null,
            'subtotal_paise' => 10000,
            'delivery_fee_paise' => 0,
            'total_paise' => 10000,
            'payment_method' => 'online',
            'payment_status' => 'pending',
            'razorpay_order_id' => 'order_stale_123',
            'status' => 'placed',
        ]);

        $activeOrder = Order::create([
            'user_id' => $user->id,
            'order_number' => 'AHM-ACTIVE001',
            'customer_name' => 'Test User',
            'customer_phone' => '9876543210',
            'delivery_pin' => '380001',
            'delivery_area' => 'Ahmedabad',
            'address_line1' => 'Line 1',
            'address_line2' => null,
            'subtotal_paise' => 10000,
            'delivery_fee_paise' => 0,
            'total_paise' => 10000,
            'payment_method' => 'online',
            'payment_status' => 'pending',
            'razorpay_order_id' => 'order_active_456',
            'status' => 'placed',
        ]);

        $response = $this->actingAs($user)->postJson(route('checkout.verify'), [
            'razorpay_order_id' => 'order_active_456',
            'razorpay_payment_id' => 'pay_123',
            'razorpay_signature' => 'sig_123',
            'order_id' => $staleOrder->id,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'ok' => false,
                'message' => 'Payment verification failed. Please contact support.',
            ]);

        $this->assertSame('pending', $staleOrder->fresh()->payment_status);
        $this->assertSame('placed', $staleOrder->fresh()->status);
        $this->assertSame('failed', $activeOrder->fresh()->payment_status);
        $this->assertSame('payment_failed', $activeOrder->fresh()->status);
    }

    public function test_payment_verification_is_idempotent_for_already_confirmed_paid_order(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'AHM-PAID001',
            'customer_name' => 'Test User',
            'customer_phone' => '9876543210',
            'delivery_pin' => '380001',
            'delivery_area' => 'Ahmedabad',
            'address_line1' => 'Line 1',
            'address_line2' => null,
            'subtotal_paise' => 10000,
            'delivery_fee_paise' => 0,
            'total_paise' => 10000,
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'razorpay_order_id' => 'order_paid_123',
            'razorpay_payment_id' => 'pay_paid_123',
            'razorpay_signature' => 'sig_paid_123',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->postJson(route('checkout.verify'), [
            'razorpay_order_id' => 'order_paid_123',
            'razorpay_payment_id' => 'pay_paid_123',
            'razorpay_signature' => 'sig_paid_123',
            'order_id' => $order->id,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'redirect_url' => route('checkout.thankyou', $order),
            ]);
    }

    public function test_marking_manual_refund_processed_restocks_undispatched_items_and_writes_single_audit_log(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create();
        $category = Category::create(['name' => 'Pain Relief', 'slug' => 'pain-relief']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'name' => 'Paracetamol',
            'slug' => 'paracetamol',
            'manufacturer' => 'ACME',
            'description' => 'Test medicine',
            'mrp_paise' => 15000,
            'price_paise' => 10000,
            'prescription_required' => false,
            'stock' => 5,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'AHM-TEST002',
            'customer_name' => 'Customer',
            'customer_phone' => '9876543210',
            'delivery_pin' => '380001',
            'delivery_area' => 'Ahmedabad',
            'address_line1' => 'Line 1',
            'address_line2' => null,
            'subtotal_paise' => 20000,
            'delivery_fee_paise' => 0,
            'total_paise' => 20000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'status' => 'refund_initiated',
            'is_dispatched' => false,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'medicine_id' => $medicine->id,
            'quantity' => 2,
            'unit_price_paise' => 10000,
            'line_total_paise' => 20000,
            'medicine_name_snapshot' => 'Paracetamol',
        ]);

        $refund = Refund::create([
            'order_id' => $order->id,
            'refund_number' => 'RFD-TEST01',
            'amount_paise' => 20000,
            'status' => 'approved',
            'type' => 'cod_bank_transfer',
            'reason' => 'Damaged item',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.refunds.markProcessed', $refund), [
            'admin_notes' => 'BANK-REF-001',
        ]);

        $response->assertRedirect();

        $this->assertSame('processed', $refund->fresh()->status);
        $this->assertSame('refunded', $order->fresh()->status);
        $this->assertSame('refunded', $order->fresh()->payment_status);
        $this->assertSame(7, $medicine->fresh()->stock);
        $this->assertCount(1, $refund->fresh()->auditLogs);
        $this->assertSame('processed', $refund->fresh()->auditLogs->first()->to_status);
    }

    public function test_refund_ineligible_message_uses_configured_refund_window(): void
    {
        $user = User::factory()->create();
        Setting::set('refund_window_days', '10');

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'AHM-TEST003',
            'customer_name' => 'Customer',
            'customer_phone' => '9876543210',
            'delivery_pin' => '380001',
            'delivery_area' => 'Ahmedabad',
            'address_line1' => 'Line 1',
            'address_line2' => null,
            'subtotal_paise' => 10000,
            'delivery_fee_paise' => 0,
            'total_paise' => 10000,
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'status' => 'delivered',
        ]);
        $order->timestamps = false;
        $order->forceFill([
            'created_at' => now()->subDays(11),
            'updated_at' => now()->subDays(11),
        ])->save();

        $response = $this
            ->actingAs($user)
            ->from(route('orders.show', $order))
            ->get(route('refunds.create', $order));

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('error', 'Refund window has expired. Refunds are only allowed within 10 days of order placement.');
    }

    public function test_razorpay_webhook_requires_valid_signature(): void
    {
        $response = $this->postJson(route('webhooks.razorpay'), [
            'event' => 'refund.processed',
        ]);

        $response->assertStatus(401);
    }

    public function test_refund_processed_webhook_updates_refund_and_restock_when_not_dispatched(): void
    {
        $customer = User::factory()->create();
        $category = Category::create(['name' => 'General', 'slug' => 'general']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'name' => 'Ibuprofen',
            'slug' => 'ibuprofen',
            'manufacturer' => 'ACME',
            'description' => 'Test medicine',
            'mrp_paise' => 20000,
            'price_paise' => 15000,
            'prescription_required' => false,
            'stock' => 3,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'AHM-WEBHOOK1',
            'customer_name' => 'Webhook User',
            'customer_phone' => '9876543210',
            'delivery_pin' => '380001',
            'delivery_area' => 'Ahmedabad',
            'address_line1' => 'Line 1',
            'address_line2' => null,
            'subtotal_paise' => 15000,
            'delivery_fee_paise' => 0,
            'total_paise' => 15000,
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'razorpay_payment_id' => 'pay_webhook_123',
            'status' => 'refund_initiated',
            'is_dispatched' => false,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'medicine_id' => $medicine->id,
            'quantity' => 1,
            'unit_price_paise' => 15000,
            'line_total_paise' => 15000,
            'medicine_name_snapshot' => 'Ibuprofen',
        ]);

        $refund = Refund::create([
            'order_id' => $order->id,
            'refund_number' => 'RFD-WEBHOOK1',
            'refund_id_gateway' => 'rfnd_123',
            'amount_paise' => 15000,
            'status' => 'processing',
            'type' => 'gateway',
            'reason' => 'Damaged',
        ]);

        $payload = json_encode([
            'event' => 'refund.processed',
            'payload' => [
                'refund' => [
                    'entity' => [
                        'id' => 'rfnd_123',
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $signature = hash_hmac('sha256', $payload, 'rzp_test_webhook_secret');

        $response = $this->call(
            'POST',
            route('webhooks.razorpay'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            ],
            $payload,
        );

        $response->assertOk();
        $this->assertSame('processed', $refund->fresh()->status);
        $this->assertSame('refunded', $order->fresh()->status);
        $this->assertSame('refunded', $order->fresh()->payment_status);
        $this->assertSame(4, $medicine->fresh()->stock);
    }
}
