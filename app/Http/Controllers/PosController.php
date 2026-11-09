<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\CheckoutRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** POS screen + checkout (FR-4.1 to FR-4.9). */
class PosController extends Controller
{
    public function index(): View
    {
        return view('pos.index', [
            'customers' => Customer::where('is_active', true)->orderByDesc('is_walk_in')->orderBy('name')->get(),
        ]);
    }

    /** AJAX product search for the POS screen (FR-4.1, NFR-2). */
    public function products(Request $request): JsonResponse
    {
        $q = $request->string('q');
        $products = Product::where('is_active', true)
            ->when($q->isNotEmpty(), fn ($x) => $x->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('sku', 'like', "%$q%")))
            ->orderBy('name')->limit(20)
            ->get(['id', 'name', 'sku', 'sale_price', 'stock', 'image'])
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'sale_price' => $product->sale_price,
                'stock' => $product->stock,
                'image_url' => $product->image ? asset('storage/'.$product->image) : null,
            ]);

        return response()->json($products);
    }

    public function checkout(CheckoutRequest $request, SaleService $service): RedirectResponse
    {
        try {
            $sale = $service->createSale($request->validated(), $request->user());
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['items' => $e->getMessage()])->withInput();
        }

        return redirect()->route('sales.show', $sale)->with('status', "Sale {$sale->invoice_no} completed.");
    }
}
