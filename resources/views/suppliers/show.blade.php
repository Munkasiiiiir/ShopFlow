<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Supplier Details</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<div class="text-sm space-y-1">
  <p><b>{{ $supplier->name }}</b> {{ $supplier->company ? '('.$supplier->company.')' : '' }}</p>
  <p>Phone: {{ $supplier->phone }} &nbsp; Email: {{ $supplier->email ?? '—' }}</p>
  <p>Address: {{ $supplier->address ?? '—' }} &nbsp; Status: {{ $supplier->is_active ? 'Active' : 'Inactive' }}</p>
  <p><b>Total due: {{ number_format($supplier->totalDue(),2) }}</b>
  <a class="ml-2 text-blue-600" href="{{ route('suppliers.ledger', $supplier) }}">View ledger</a>
  <a class="ml-2 text-blue-600" href="{{ route('suppliers.ledger.pdf', $supplier) }}">Ledger PDF</a></p></div>
<h3 class="mt-6 mb-2 font-semibold">Purchase history</h3>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Reference</th><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Total</th><th class="py-2 pr-4">Paid</th><th class="py-2">Due</th></tr></thead>
<tbody>@forelse($supplier->purchases as $p)
<tr class="border-b"><td class="py-2 pr-4"><a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('purchases.show',$p) }}">{{ $p->reference_no }}</a></td>
<td class="py-2 pr-4">{{ $p->purchase_date->format('Y-m-d') }}</td><td class="py-2 pr-4">{{ $p->total_amount }}</td>
<td class="py-2 pr-4">{{ $p->paid_amount }}</td><td class="py-2">{{ $p->due_amount }}</td></tr>
@empty<tr><td colspan="5" class="py-4 text-center text-gray-500">No purchases.</td></tr>@endforelse</tbody></table>
    </div></div></div>
</x-app-layout>
