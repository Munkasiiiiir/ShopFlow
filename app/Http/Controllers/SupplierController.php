<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Suppliers, ledger + PDF (FR-3.1, FR-3.2, FR-3.7, FR-3.10). */
class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $suppliers = Supplier::withSum('purchases as due_total', 'due_amount')
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w->where('name', 'like', "%$v%")->orWhere('company', 'like', "%$v%")->orWhere('phone', 'like', "%$v%")))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->rules($request) + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('suppliers.index')->with('status', 'Supplier saved.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['purchases.items', 'payments.purchase']);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->rules($request) + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('suppliers.index')->with('status', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return back()->with('status', 'Supplier deleted.');
    }

    public function toggleActive(Supplier $supplier): RedirectResponse
    {
        $supplier->update(['is_active' => ! $supplier->is_active]);

        return back()->with('status', 'Supplier status updated.');
    }

    public function ledger(Supplier $supplier): View
    {
        $supplier->load(['purchases', 'payments.purchase']);

        return view('suppliers.ledger', compact('supplier'));
    }

    public function ledgerPdf(Supplier $supplier)
    {
        $supplier->load(['purchases', 'payments.purchase']);

        return Pdf::loadView('pdf.supplier-ledger', compact('supplier'))
            ->download("ledger-{$supplier->id}.pdf");
    }

    private function rules(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'company' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
        ]);
    }
}
