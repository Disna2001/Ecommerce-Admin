<?php

namespace Tests\Feature;

use App\Jobs\SendInvoiceEmailJob;
use App\Jobs\SendOrderStatusEmailJob;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Livewire\Admin\AdminActivityLogManager;
use App\Livewire\Admin\OrderManager;
use App\Livewire\InvoiceManager;
use App\Livewire\PosInvoice;
use App\Livewire\Shop\Checkout;
use App\Models\AdminActivityLog;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\NotificationOutbox;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Models\StockMovementLog;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_decrements_stock_and_logs_activity(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $stock = $this->createStock(quantity: 5, price: 1500);

        SiteSetting::set('whatsapp_enabled', '1', 'boolean', 'whatsapp');
        SiteSetting::set('whatsapp_api_url', 'https://example.test/webhook', 'text', 'whatsapp');

        session([
            'cart' => [
                $stock->id => [
                    'name' => $stock->name,
                    'sku' => $stock->sku,
                    'price' => 1500,
                    'original_price' => 1500,
                    'quantity' => 2,
                ],
            ],
            'cart_discount' => 0,
        ]);

        $this->actingAs($user);

        Livewire::test(Checkout::class)
            ->set('first_name', 'Dishna')
            ->set('last_name', 'Admin')
            ->set('email', 'buyer@example.com')
            ->set('phone', '0712345678')
            ->set('address', '123 Main Street')
            ->set('city', 'Colombo')
            ->set('postal_code', '10100')
            ->set('payment_method', 'cod')
            ->call('placeOrder')
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'buyer@example.com',
            'status' => 'pending',
        ]);

        $order = Order::first();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'stock_id' => $stock->id,
            'quantity' => 2,
        ]);
        $this->assertSame(3, $stock->fresh()->quantity);
        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'order.created',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
        ]);
        $this->assertDatabaseHas('notification_outboxes', [
            'channel' => 'email',
            'related_type' => Order::class,
            'related_id' => $order->id,
            'status' => 'queued',
        ]);
        $this->assertDatabaseHas('notification_outboxes', [
            'channel' => 'whatsapp',
            'related_type' => Order::class,
            'related_id' => $order->id,
            'status' => 'queued',
        ]);
        $this->assertDatabaseHas('stock_movement_logs', [
            'stock_id' => $stock->id,
            'direction' => 'out',
            'quantity' => 2,
            'reference_type' => Order::class,
            'reference_id' => $order->id,
        ]);

        Queue::assertPushed(SendOrderStatusEmailJob::class);
        Queue::assertPushed(SendWhatsAppNotificationJob::class);
    }

    public function test_admin_payment_review_updates_order_and_creates_audit_entry(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'user_id' => $admin->id,
            'customer_name' => 'Payment User',
            'customer_email' => 'payment@example.com',
            'customer_phone' => '0712345678',
            'shipping_address' => 'No. 1',
            'shipping_city' => 'Colombo',
            'shipping_country' => 'Sri Lanka',
            'status' => 'pending',
            'subtotal' => 2000,
            'discount' => 0,
            'shipping_fee' => 0,
            'total' => 2000,
            'payment_method' => 'bank',
            'payment_status' => 'unpaid',
            'payment_review_status' => 'pending_review',
        ]);

        SiteSetting::set('whatsapp_enabled', '1', 'boolean', 'whatsapp');
        SiteSetting::set('whatsapp_api_url', 'https://example.test/webhook', 'text', 'whatsapp');

        $this->actingAs($admin);

        Livewire::test(OrderManager::class)
            ->set('paymentOrderId', $order->id)
            ->set('paymentDecision', 'approve')
            ->set('paymentReviewNote', 'Verified against bank slip.')
            ->call('verifyPayment');

        $order->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('approved', $order->payment_review_status);
        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'order.payment_reviewed',
            'subject_id' => $order->id,
        ]);

        Queue::assertPushed(SendOrderStatusEmailJob::class);
        Queue::assertPushed(SendWhatsAppNotificationJob::class);
    }

    public function test_pos_sale_creates_invoice_reduces_stock_and_logs_activity(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $stock = $this->createStock(quantity: 4, price: 2500);

        $this->actingAs($admin);

        Livewire::test(PosInvoice::class)
            ->set('cart', [[
                'id' => 'line-1',
                'stock_id' => $stock->id,
                'name' => $stock->name,
                'sku' => $stock->sku,
                'quantity' => 2,
                'unit_price' => 2500,
                'discount' => 0,
                'tax_rate' => 0,
                'total' => 5000,
                'stock_quantity' => 4,
            ]])
            ->call('calculateCart')
            ->set('customer_name', 'POS Customer')
            ->set('customer_email', 'pos@example.com')
            ->set('payment_method', 'cash')
            ->set('sendInvoiceEmail', false)
            ->set('amount_paid', 5000)
            ->call('processPayment');

        $invoice = Invoice::first();

        $this->assertNotNull($invoice);
        $this->assertSame(2, $stock->fresh()->quantity);
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'stock_id' => $stock->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'pos.invoice_created',
            'subject_id' => $invoice->id,
        ]);
        $this->assertDatabaseHas('stock_movement_logs', [
            'stock_id' => $stock->id,
            'direction' => 'out',
            'quantity' => 2,
            'reference_type' => Invoice::class,
            'reference_id' => $invoice->id,
        ]);
    }

    public function test_invoice_cancellation_restores_stock_and_logs_activity(): void
    {
        $admin = User::factory()->create();
        $stock = $this->createStock(quantity: 1, price: 3000);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-001',
            'user_id' => $admin->id,
            'customer_name' => 'Invoice Customer',
            'customer_email' => 'invoice@example.com',
            'invoice_date' => now(),
            'subtotal' => 6000,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 6000,
            'amount_paid' => 0,
            'balance_due' => 6000,
            'status' => 'sent',
            'payment_method' => 'cash',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'stock_id' => $stock->id,
            'item_name' => $stock->name,
            'item_code' => $stock->item_code,
            'quantity' => 2,
            'unit_price' => 3000,
            'discount' => 0,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'total' => 6000,
        ]);

        $stock->update(['quantity' => 1]);

        $this->actingAs($admin);

        Livewire::test(InvoiceManager::class)
            ->call('markAsCancelled', $invoice->id);

        $this->assertSame(3, $stock->fresh()->quantity);
        $this->assertSame('cancelled', $invoice->fresh()->status);
        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'invoice.cancelled',
            'subject_id' => $invoice->id,
        ]);
        $this->assertDatabaseHas('stock_movement_logs', [
            'stock_id' => $stock->id,
            'direction' => 'in',
            'quantity' => 2,
            'reference_type' => Invoice::class,
            'reference_id' => $invoice->id,
        ]);
    }

    public function test_activity_log_page_exports_and_resolves_links(): void
    {
        $admin = User::factory()->create();
        $order = Order::create([
            'order_number' => 'ORD-TEST-002',
            'user_id' => $admin->id,
            'customer_name' => 'Linked User',
            'customer_email' => 'linked@example.com',
            'customer_phone' => '0712345678',
            'shipping_address' => 'No. 2',
            'shipping_city' => 'Kandy',
            'shipping_country' => 'Sri Lanka',
            'status' => 'pending',
            'subtotal' => 1000,
            'discount' => 0,
            'shipping_fee' => 0,
            'total' => 1000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
        ]);

        AdminActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'order.created',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'description' => 'Created from checkout.',
            'properties' => ['order_number' => $order->order_number],
        ]);

        $this->actingAs($admin);
        Permission::firstOrCreate(['name' => 'view orders', 'guard_name' => 'web']);
        $admin->givePermissionTo('view orders');

        Livewire::test(AdminActivityLogManager::class)
            ->call('openDetailModal', AdminActivityLog::first()->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('Open Order')
            ->assertSee('Created from checkout.');

        $this->get(route('admin.orders', ['focusOrder' => $order->id]))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_email_jobs_mark_outbox_entries_as_sent(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $order = Order::create([
            'order_number' => 'ORD-TEST-EMAIL',
            'user_id' => $user->id,
            'customer_name' => 'Email User',
            'customer_email' => 'ordermail@example.com',
            'customer_phone' => '0712345678',
            'shipping_address' => 'No. 10',
            'shipping_city' => 'Colombo',
            'shipping_country' => 'Sri Lanka',
            'status' => 'pending',
            'subtotal' => 2500,
            'discount' => 0,
            'shipping_fee' => 0,
            'total' => 2500,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-EMAIL',
            'user_id' => $user->id,
            'customer_name' => 'Invoice Mail User',
            'customer_email' => 'invoicemail@example.com',
            'invoice_date' => now(),
            'subtotal' => 2500,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 2500,
            'amount_paid' => 2500,
            'balance_due' => 0,
            'status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $orderOutbox = NotificationOutbox::create([
            'channel' => 'email',
            'recipient' => $order->customer_email,
            'subject' => 'Order update',
            'status' => 'queued',
            'provider' => 'array',
            'related_type' => Order::class,
            'related_id' => $order->id,
            'payload' => ['stage' => 'created', 'message' => 'Testing'],
            'queued_at' => now(),
        ]);

        $invoiceOutbox = NotificationOutbox::create([
            'channel' => 'email',
            'recipient' => $invoice->customer_email,
            'subject' => 'Invoice update',
            'status' => 'queued',
            'provider' => 'array',
            'related_type' => Invoice::class,
            'related_id' => $invoice->id,
            'payload' => ['invoice_number' => $invoice->invoice_number],
            'queued_at' => now(),
        ]);

        (new SendOrderStatusEmailJob($order->id, 'created', 'Testing', $orderOutbox->id))->handle();
        (new SendInvoiceEmailJob($invoice->id, $invoiceOutbox->id))->handle();

        $this->assertSame('sent', $orderOutbox->fresh()->status);
        $this->assertNotNull($orderOutbox->fresh()->sent_at);
        $this->assertSame('sent', $invoiceOutbox->fresh()->status);
        $this->assertNotNull($invoiceOutbox->fresh()->sent_at);
        $this->assertNotNull($invoice->fresh()->email_sent_at);
    }

    protected function createStock(int $quantity = 5, int $price = 1000): Stock
    {
        $category = Category::create([
            'name' => 'Displays',
            'slug' => 'displays',
        ]);

        $supplier = Supplier::create([
            'name' => 'Main Supplier',
            'email' => 'supplier@example.com',
        ]);

        return Stock::create([
            'sku' => 'SKU-' . fake()->unique()->numerify('#####'),
            'item_code' => 'ITM-' . fake()->unique()->numerify('#####'),
            'name' => 'Test Stock',
            'description' => 'Test item',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'quantity' => $quantity,
            'reorder_level' => 1,
            'unit_price' => $price,
            'selling_price' => $price,
            'status' => 'active',
        ]);
    }
}
