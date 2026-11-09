<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierPaymentRequest;
use App\Models\Purchase;
use App\Models\SupplierPayment;
use App\Services\SupplierPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Supplier payments (FR-3.8, FR-3.9). */
class SupplierPaymentController extends Controller
{
    public function index(): View
    {
        $payments = SupplierPayment::with(['supplier', 'purchase', 'user'])->latest('payment_date')->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(Purchase $purchase): View
    {
        return view('payments.create', compact('purchase'));
    }

    public function store(StoreSupplierPaymentRequest $request, Purchase $purchase, SupplierPaymentService $service): RedirectResponse
    {
        $service->recordPayment($purchase, $request->validated(), $request->user());

        return redirect()->route('purchases.show', $purchase)->with('status', 'Payment recorded.');
    }
}
