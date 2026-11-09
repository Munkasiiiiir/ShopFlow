<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Current stock list with status (FR-3.6). */
class CurrentStockController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with(['category', 'unit'])
            ->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->stock_status === 'in_stock', fn ($q) => $q->whereColumn('stock', '>', 'alert_qty'))
            ->when($request->stock_status === 'low_stock', fn ($q) => $q->where('stock', '>', 0)->whereColumn('stock', '<=', 'alert_qty'))
            ->when($request->stock_status === 'out_of_stock', fn ($q) => $q->where('stock', 0))
            ->when($request->q, fn ($q, $v) => $q->where('name', 'like', "%$v%"))
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('stock.current', ['products' => $products, 'categories' => Category::orderBy('name')->get()]);
    }
}
