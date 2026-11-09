<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Customers (FR-3.3 to FR-3.5, FR-3.7). */
class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::withCount('sales')
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w->where('name', 'like', "%$v%")->orWhere('phone', 'like', "%$v%")))
            ->orderByDesc('is_walk_in')->orderBy('name')->paginate(20)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Customer::create($this->rules($request) + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('customers.index')->with('status', 'Customer saved.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['sales' => fn ($q) => $q->latest('sale_date')]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->rules($request) + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('customers.index')->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->is_walk_in) {
            return back()->withErrors(['customer' => 'The walk-in customer cannot be deleted.']);
        }
        $customer->delete();

        return back()->with('status', 'Customer deleted.');
    }

    public function toggleActive(Customer $customer): RedirectResponse
    {
        $customer->update(['is_active' => ! $customer->is_active]);

        return back()->with('status', 'Customer status updated.');
    }

    private function rules(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);
    }
}
