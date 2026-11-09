<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\StockMovementType;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Shop settings (FR-1.7)
        foreach ([
            'shop_name' => 'ShopFlow Retail',
            'shop_address' => 'Dhanmondi, Dhaka',
            'shop_phone' => '01XXXXXXXXX',
            'currency' => 'BDT',
            'tax_rate' => '0',
            'invoice_prefix' => 'INV-',
        ] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Staff accounts (password for all demo accounts: "password")
        $admin = User::updateOrCreate(['email' => 'admin@shopflow.test'], [
            'name' => 'Shop Admin', 'phone' => '01700000001', 'password' => 'password',
            'role' => UserRole::Admin, 'status' => AccountStatus::Approved, 'approved_at' => now(),
        ]);
        User::updateOrCreate(['email' => 'manager@shopflow.test'], [
            'name' => 'Shop Manager', 'phone' => '01700000002', 'password' => 'password',
            'role' => UserRole::Manager, 'status' => AccountStatus::Approved,
            'approved_by' => $admin->id, 'approved_at' => now(),
        ]);
        User::updateOrCreate(['email' => 'cashier@shopflow.test'], [
            'name' => 'Shop Cashier', 'phone' => '01700000003', 'password' => 'password',
            'role' => UserRole::Cashier, 'status' => AccountStatus::Approved,
            'approved_by' => $admin->id, 'approved_at' => now(),
        ]);

        // Catalog master data (Module 2)
        $electronics = Category::updateOrCreate(['name' => 'Electronics']);
        $grocery = Category::updateOrCreate(['name' => 'Grocery']);
        Category::updateOrCreate(['name' => 'Stationery']);
        $walton = Brand::updateOrCreate(['name' => 'Walton']);
        $fresh = Brand::updateOrCreate(['name' => 'Fresh']);
        $pcs = Unit::updateOrCreate(['short_name' => 'pcs'], ['name' => 'Piece']);
        $kg = Unit::updateOrCreate(['short_name' => 'kg'], ['name' => 'Kilogram']);
        Unit::updateOrCreate(['short_name' => 'litre'], ['name' => 'Litre']);
        Unit::updateOrCreate(['short_name' => 'box'], ['name' => 'Box']);

        // Parties (Module 3)
        $supplier = Supplier::updateOrCreate(['phone' => '01800000001'], [
            'name' => 'Rahim Uddin', 'company' => 'Rahim Enterprise',
            'email' => 'rahim@example.com', 'address' => 'Karwan Bazar, Dhaka', 'is_active' => true,
        ]);
        Customer::updateOrCreate(['is_walk_in' => true], [
            'name' => 'Walk-in Customer', 'phone' => null, 'is_active' => true,
        ]);
        Customer::updateOrCreate(['phone' => '01900000001'], [
            'name' => 'Karim Ahmed', 'address' => 'Mirpur, Dhaka', 'is_walk_in' => false, 'is_active' => true,
        ]);

        // Products start with zero stock; opening stock comes in through a purchase (FR-2.7, FR-2.8)
        $products = collect([
            ['Rice (Miniket)', 'PRD-001', $grocery->id, $fresh->id, $kg->id, 60, 75, 5],
            ['Soybean Oil 1L', 'PRD-002', $grocery->id, $fresh->id, $pcs->id, 140, 180, 5],
            ['USB Cable', 'PRD-003', $electronics->id, $walton->id, $pcs->id, 120, 200, 10],
            ['LED Bulb 9W', 'PRD-004', $electronics->id, $walton->id, $pcs->id, 90, 150, 10],
        ])->map(fn ($p) => Product::updateOrCreate(['sku' => $p[1]], [
            'name' => $p[0], 'category_id' => $p[2], 'brand_id' => $p[3], 'unit_id' => $p[4],
            'cost_price' => $p[5], 'sale_price' => $p[6], 'alert_qty' => $p[7], 'is_active' => true,
        ]));

        if (! Purchase::where('reference_no', 'PUR-0001')->exists()) {
            $purchase = Purchase::create([
                'reference_no' => 'PUR-0001', 'supplier_id' => $supplier->id, 'user_id' => $admin->id,
                'purchase_date' => now()->toDateString(),
                'total_amount' => 0, 'paid_amount' => 0, 'due_amount' => 0, 'note' => 'Opening stock (seeder)',
            ]);

            $stock = app(StockService::class);
            $total = 0;
            foreach ($products as $i => $product) {
                $qty = [50, 40, 100, 80][$i];
                $purchase->items()->create([
                    'product_id' => $product->id, 'quantity' => $qty,
                    'unit_cost' => $product->cost_price, 'subtotal' => $qty * (float) $product->cost_price,
                ]);
                $stock->increase($product, $qty, StockMovementType::Purchase, $purchase, $admin);
                $total += $qty * (float) $product->cost_price;
            }
            $purchase->update(['total_amount' => $total, 'paid_amount' => $total, 'due_amount' => 0]);
        }
    }
}
