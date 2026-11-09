<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnRequest;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Sales returns (FR-1.12). Admin/Manager. */
class SaleReturnController extends Controller
{
    public function index(): View
    {
        $returns = SaleReturn::with(['sale', 'user'])->latest('id')->paginate(20);

        return view('returns.index', compact('returns'));
    }

    public function create(Sale $sale): View
    {
        $sale->load('items.product', 'customer');

        return view('returns.create', compact('sale'));
    }

    public function store(StoreReturnRequest $request, Sale $sale, ReturnService $service): RedirectResponse
    {
        $return = $service->processReturn($sale, $request->validated()['items'],
            $request->validated()['reason'] ?? null, $request->user());

        return redirect()->route('returns.index')
            ->with('status', "Return {$return->return_no} processed. Refund: ".number_format((float) $return->total_refund, 2));
    }
}
