<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Purchases (FR-2.7 to FR-2.9). */
class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $purchases = Purchase::with(['supplier', 'user'])
            ->when($request->q, fn ($q, $v) => $q->where('reference_no', 'like', "%$v%"))
            ->when($request->supplier_id, fn ($q, $v) => $q->where('supplier_id', $v))
            ->latest('purchase_date')->paginate(20)->withQueryString();

        return view('purchases.index', ['purchases' => $purchases, 'suppliers' => Supplier::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('purchases.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku', 'cost_price', 'stock']),
        ]);
    }

    public function store(StorePurchaseRequest $request, PurchaseService $service): RedirectResponse
    {
        $purchase = $service->createPurchase($request->validated(), $request->user());

        return redirect()->route('purchases.show', $purchase)
            ->with('status', "Purchase {$purchase->reference_no} saved and stock updated.");
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product', 'payments.user']);

        return view('purchases.show', compact('purchase'));
    }
}
