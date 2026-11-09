<?php

namespace Tests\Feature\ShopFlow;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleFlowsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $manager;

    private User $cashier;

    private Product $product;

    private Supplier $supplier;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->manager = User::factory()->create(['role' => UserRole::Manager]);
        $this->cashier = User::factory()->create(['role' => UserRole::Cashier]);
        $this->product = Product::create([
            'category_id' => Category::create(['name' => 'Grocery'])->id,
            'unit_id' => Unit::create(['name' => 'Piece', 'short_name' => 'pcs'])->id,
            'name' => 'Rice', 'sku' => 'PRD-001', 'cost_price' => 50, 'sale_price' => 80,
            'stock' => 0, 'alert_qty' => 5,
        ]);
        $this->supplier = Supplier::create(['name' => 'Rahim', 'phone' => '018']);
        $this->customer = Customer::create(['name' => 'Walk-in Customer', 'is_walk_in' => true]);
    }

    private function purchase(int $qty = 20, float $paid = 0): Purchase
    {
        $this->actingAs($this->manager)->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'paid_amount' => $paid,
            'items' => [['product_id' => $this->product->id, 'quantity' => $qty, 'unit_cost' => 55]],
        ])->assertRedirect();

        return Purchase::latest('id')->first();
    }

    public function test_purchase_increases_stock_updates_cost_and_due(): void
    {
        $purchase = $this->purchase(20, 500);

        $this->assertSame(20, $this->product->fresh()->stock);
        $this->assertSame('55.00', $this->product->fresh()->cost_price);
        $this->assertSame('1100.00', $purchase->total_amount);
        $this->assertSame('600.00', $purchase->due_amount);
        $this->assertStringStartsWith('PUR-', $purchase->reference_no);
    }

    public function test_supplier_payment_reduces_due_and_overpayment_rejected(): void
    {
        $purchase = $this->purchase(10, 0); // total 550, due 550

        $this->actingAs($this->manager)->post(route('payments.store', $purchase), [
            'amount' => 200, 'payment_date' => now()->toDateString(), 'payment_method' => 'cash',
        ])->assertRedirect(route('purchases.show', $purchase));
        $this->assertSame('350.00', $purchase->fresh()->due_amount);

        $this->actingAs($this->manager)->post(route('payments.store', $purchase), [
            'amount' => 999, 'payment_date' => now()->toDateString(), 'payment_method' => 'cash',
        ])->assertSessionHasErrors('amount');
        $this->assertSame('350.00', $purchase->fresh()->due_amount);
    }

    public function test_pos_checkout_creates_sale_decreases_stock_and_sets_invoice(): void
    {
        $this->purchase(20, 1100);

        $this->actingAs($this->cashier)->post(route('pos.checkout'), [
            'customer_id' => $this->customer->id,
            'payment_method' => 'cash', 'paid_amount' => 200, 'discount' => 0, 'tax' => 0,
            'items' => [['product_id' => $this->product->id, 'quantity' => 2]],
        ])->assertRedirect();

        $sale = Sale::first();
        $this->assertNotNull($sale->invoice_no);
        $this->assertSame('160.00', $sale->grand_total);
        $this->assertSame('40.00', $sale->change_amount);
        $this->assertSame(18, $this->product->fresh()->stock);
        $this->assertSame('80.00', $sale->items->first()->unit_price); // snapshot
    }

    public function test_sale_with_insufficient_stock_rolls_back_completely(): void
    {
        $this->purchase(3, 165);

        $this->actingAs($this->cashier)->post(route('pos.checkout'), [
            'customer_id' => $this->customer->id,
            'payment_method' => 'cash', 'paid_amount' => 1000,
            'items' => [['product_id' => $this->product->id, 'quantity' => 10]],
        ])->assertSessionHasErrors('items');

        $this->assertSame(0, Sale::count());
        $this->assertSame(3, $this->product->fresh()->stock);
    }

    public function test_partial_return_restores_stock_and_updates_status(): void
    {
        $this->purchase(20, 1100);
        $this->actingAs($this->cashier)->post(route('pos.checkout'), [
            'customer_id' => $this->customer->id,
            'payment_method' => 'cash', 'paid_amount' => 500,
            'items' => [['product_id' => $this->product->id, 'quantity' => 5]],
        ]);
        $sale = Sale::first();
        $this->assertSame(15, $this->product->fresh()->stock);

        $this->actingAs($this->manager)->post(route('returns.store', $sale), [
            'reason' => 'Damaged packet',
            'items' => [['sale_item_id' => $sale->items->first()->id, 'quantity' => 2]],
        ])->assertRedirect(route('returns.index'));

        $this->assertSame(17, $this->product->fresh()->stock);
        $this->assertSame('partially_returned', $sale->fresh()->status->value);
        $this->assertSame(2, $sale->items->first()->fresh()->returned_qty);
    }

    public function test_product_without_sku_gets_auto_sku(): void
    {
        $this->actingAs($this->manager)->post(route('products.store'), [
            'name' => 'New Item', 'category_id' => $this->product->category_id,
            'unit_id' => $this->product->unit_id, 'cost_price' => 10, 'sale_price' => 15, 'alert_qty' => 3,
        ])->assertRedirect(route('products.index'));

        $this->assertNotNull(Product::where('name', 'New Item')->first()->sku);
        $this->assertStringStartsWith('PRD-', Product::where('name', 'New Item')->first()->sku);
    }

    public function test_cashier_cannot_open_reports_but_can_open_dashboard_and_pos(): void
    {
        $this->actingAs($this->cashier)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($this->cashier)->get(route('dashboard'))->assertOk();
        $this->actingAs($this->cashier)->get(route('pos.index'))->assertOk();
        $this->actingAs($this->manager)->get(route('reports.sales'))->assertOk();
    }

    public function test_admin_can_update_settings_and_invoice_uses_prefix(): void
    {
        $this->actingAs($this->admin)->patch(route('settings.update'), [
            'shop_name' => 'My Shop', 'currency' => 'BDT', 'tax_rate' => 5, 'invoice_prefix' => 'MS-',
        ])->assertRedirect();
        $this->assertSame('My Shop', Setting::getValue('shop_name'));

        $this->purchase(5, 275);
        $this->actingAs($this->cashier)->post(route('pos.checkout'), [
            'customer_id' => $this->customer->id, 'payment_method' => 'cash', 'paid_amount' => 100,
            'items' => [['product_id' => $this->product->id, 'quantity' => 1]],
        ]);
        $this->assertStringStartsWith('MS-', Sale::first()->invoice_no);
    }
}
