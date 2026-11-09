<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurrentStockController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

// ShopFlow is an internal system: home goes to the dashboard, or to login for guests.
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Catalog read access for all approved staff (M2)
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');

    // Parties read access (M3); customers can also be viewed/added by cashiers for POS
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    // Current stock list (M3, FR-3.6)
    Route::get('stock', [CurrentStockController::class, 'index'])->name('stock.current');

    // POS & sales (M4): all roles sell; cashiers only see their own sales (in controller)
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('pos/products', [PosController::class, 'products'])->name('pos.products');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('sales/{sale}/invoice.pdf', [SaleController::class, 'invoicePdf'])->name('sales.invoice.pdf');
});

// Admin + Manager only
Route::middleware(['auth', 'approved', 'role:admin,manager'])->group(function () {
    // Catalog management (M2)
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('brands', BrandController::class)->except(['show']);
    Route::resource('units', UnitController::class)->except(['show']);
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Purchases (M2)
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

    // Suppliers & customers management (M3)
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::post('suppliers/{supplier}/toggle-active', [SupplierController::class, 'toggleActive'])->name('suppliers.toggle');
    Route::get('suppliers/{supplier}/ledger', [SupplierController::class, 'ledger'])->name('suppliers.ledger');
    Route::get('suppliers/{supplier}/ledger.pdf', [SupplierController::class, 'ledgerPdf'])->name('suppliers.ledger.pdf');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::post('customers/{customer}/toggle-active', [CustomerController::class, 'toggleActive'])->name('customers.toggle');

    // Supplier payments (M3)
    Route::get('payments', [SupplierPaymentController::class, 'index'])->name('payments.index');
    Route::get('purchases/{purchase}/payment', [SupplierPaymentController::class, 'create'])->name('payments.create');
    Route::post('purchases/{purchase}/payment', [SupplierPaymentController::class, 'store'])->name('payments.store');

    // Stock movements & adjustments (M1)
    Route::get('stock/movements', [StockMovementController::class, 'index'])->name('stock.movements');
    Route::get('stock/adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
    Route::post('stock/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');

    // Sales returns (M1)
    Route::get('returns', [SaleReturnController::class, 'index'])->name('returns.index');
    Route::get('sales/{sale}/return', [SaleReturnController::class, 'create'])->name('returns.create');
    Route::post('sales/{sale}/return', [SaleReturnController::class, 'store'])->name('returns.store');

    // Reports (M5)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('sales.pdf', [ReportController::class, 'salesPdf'])->name('sales.pdf');
        Route::get('sales.csv', [ReportController::class, 'salesCsv'])->name('sales.csv');
        Route::get('top-products', [ReportController::class, 'topProducts'])->name('top-products');
        Route::get('stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('profit', [ReportController::class, 'profit'])->name('profit');
        Route::get('purchases', [ReportController::class, 'purchases'])->name('purchases');
    });
});

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
});

// Admin only (M1)
Route::middleware(['auth', 'approved', 'role:admin'])->group(function () {
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::post('{user}/approve', [StaffController::class, 'approve'])->name('approve');
        Route::post('{user}/reject', [StaffController::class, 'reject'])->name('reject');
        Route::patch('{user}/role', [StaffController::class, 'updateRole'])->name('role');
        Route::post('{user}/toggle-active', [StaffController::class, 'toggleActive'])->name('toggle');
    });
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::patch('settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
