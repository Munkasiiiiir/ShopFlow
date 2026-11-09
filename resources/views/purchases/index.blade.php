<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Purchase History</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="GET" class="mb-4 flex flex-wrap gap-2">
  <input name="q" value="{{ request('q') }}" placeholder="Reference no" class="sf-input max-w-xs">
  <select name="supplier_id" class="sf-select"><option value="">All suppliers</option>
  @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(request('supplier_id')==$s->id)>{{ $s->name }}</option>@endforeach</select>
  <button class="sf-btn-secondary">Search</button>
  <a href="{{ route('purchases.create') }}" class="sf-btn-primary">+ New Purchase</a></form>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Reference</th><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Supplier</th><th class="py-2 pr-4">Total</th><th class="py-2 pr-4">Paid</th><th class="py-2">Due</th></tr></thead>
<tbody>@forelse($purchases as $p)
<tr class="border-b"><td class="py-2 pr-4"><a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('purchases.show',$p) }}">{{ $p->reference_no }}</a></td>
<td class="py-2 pr-4">{{ $p->purchase_date->format('Y-m-d') }}</td><td class="py-2 pr-4">{{ $p->supplier?->name }}</td>
<td class="py-2 pr-4">{{ $p->total_amount }}</td><td class="py-2 pr-4">{{ $p->paid_amount }}</td><td class="py-2">{{ $p->due_amount }}</td></tr>
@empty<tr><td colspan="6" class="py-4 text-center text-gray-500">No purchases.</td></tr>@endforelse</tbody></table>
<div class="mt-4">{{ $purchases->links() }}</div>
    </div></div></div>
</x-app-layout>
