<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Product catalog (FR-2.1 to FR-2.6, FR-2.10 to FR-2.12). */
class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with(['category', 'brand', 'unit'])
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w->where('name', 'like', "%$v%")->orWhere('sku', 'like', "%$v%")))
            ->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->brand_id, fn ($q, $v) => $q->where('brand_id', $v))
            ->when($request->stock_status === 'in_stock', fn ($q) => $q->whereColumn('stock', '>', 'alert_qty'))
            ->when($request->stock_status === 'low_stock', fn ($q) => $q->where('stock', '>', 0)->whereColumn('stock', '<=', 'alert_qty'))
            ->when($request->stock_status === 'out_of_stock', fn ($q) => $q->where('stock', 0))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function lowStock(): View
    {
        $products = Product::with(['category', 'unit'])->whereColumn('stock', '<=', 'alert_qty')
            ->orderBy('stock')->paginate(20);

        return view('products.low-stock', compact('products'));
    }

    public function create(): View
    {
        return view('products.create', $this->formData());
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $product = Product::create(collect($data)->except('image')->all() + [
            'sku' => $data['sku'] ?? 'TMP-'.uniqid(),
            'image' => $data['image'] ?? null,
        ]);
        if (empty($request->sku)) { // FR-2.12: auto SKU
            $product->update(['sku' => 'PRD-'.str_pad((string) $product->id, 4, '0', STR_PAD_LEFT)]);
        }

        return redirect()->route('products.index')->with('status', "Product {$product->sku} saved.");
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'unit']);
        $movements = $product->stockMovements()->latest('id')->limit(10)->get();

        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', $this->formData() + ['product' => $product]);
    }

    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($data['image']);
        }
        $product->update($data);

        return redirect()->route('products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete(); // soft delete (FR-2.4)

        return back()->with('status', 'Product deleted.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
        ];
    }
}
