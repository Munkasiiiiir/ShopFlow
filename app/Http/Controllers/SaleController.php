<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Sale;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Sales list, details and invoice (FR-4.9, FR-4.10). */
class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::with(['customer', 'user'])
            ->when($request->user()->role === UserRole::Cashier, fn ($q) => $q->where('user_id', $request->user()->id))
            ->when($request->q, fn ($q, $v) => $q->where('invoice_no', 'like', "%$v%"))
            ->when($request->date, fn ($q, $v) => $q->whereDate('sale_date', $v))
            ->when($request->user_id, fn ($q, $v) => $q->where('user_id', $v))
            ->latest('sale_date')->paginate(20)->withQueryString();

        return view('sales.index', ['sales' => $sales, 'cashiers' => User::orderBy('name')->get()]);
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'user', 'items.product', 'returns']);

        return view('sales.show', compact('sale'));
    }

    public function invoicePdf(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);

        return Pdf::loadView('pdf.invoice', compact('sale'))->download("{$sale->invoice_no}.pdf");
    }
}
