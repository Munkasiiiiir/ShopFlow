<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Stock movement history (FR-1.10). */
class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $movements = StockMovement::with(['product', 'user'])
            ->when($request->product_id, fn ($q, $v) => $q->where('product_id', $v))
            ->when($request->type, fn ($q, $v) => $q->where('type', $v))
            ->latest('id')->paginate(25)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('stock.movements', compact('movements', 'products'));
    }
}
