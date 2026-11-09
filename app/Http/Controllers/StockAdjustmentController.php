<?php

namespace App\Http\Controllers;

use App\Enums\AdjustmentType;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Manual stock adjustments (FR-1.11). Admin/Manager. */
class StockAdjustmentController extends Controller
{
    public function index(): View
    {
        $adjustments = StockAdjustment::with(['product', 'user'])->latest('id')->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('stock.adjustments', compact('adjustments', 'products'));
    }

    public function store(Request $request, StockService $stock): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::enum(AdjustmentType::class)],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', 'string'],
        ]);
        try {
            $stock->adjust(Product::findOrFail($data['product_id']), $data['quantity'],
                AdjustmentType::from($data['type']), $data['reason'], $request->user());
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        return back()->with('status', 'Stock adjusted.');
    }
}
