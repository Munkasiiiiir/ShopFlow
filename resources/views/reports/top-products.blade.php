<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Top-Selling Products</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="GET" class="mb-4 flex gap-2 items-end">
  <label class="text-sm">From <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="sf-select block"></label>
  <label class="text-sm">To <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="sf-select block"></label>
  <button class="sf-btn-secondary">Filter</button></form>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">#</th><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">SKU</th><th class="py-2 pr-4">Qty sold</th><th class="py-2">Amount</th></tr></thead>
<tbody>@forelse($products as $i => $p)
<tr class="border-b"><td class="py-2 pr-4">{{ $i+1 }}</td><td class="py-2 pr-4">{{ $p->name }}</td><td class="py-2 pr-4">{{ $p->sku }}</td>
<td class="py-2 pr-4">{{ $p->qty }}</td><td class="py-2">{{ number_format($p->amount,2) }}</td></tr>
@empty<tr><td colspan="5" class="py-4 text-center text-gray-500">No sales yet.</td></tr>@endforelse</tbody></table>
    </div></div></div>
</x-app-layout>
