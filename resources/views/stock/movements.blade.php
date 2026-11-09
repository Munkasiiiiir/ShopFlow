<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Stock Movement History</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="GET" class="mb-4 flex flex-wrap gap-2">
  <select name="product_id" class="sf-select"><option value="">All products</option>
    @foreach($products as $p)<option value="{{ $p->id }}" @selected(request('product_id')==$p->id)>{{ $p->name }}</option>@endforeach</select>
  <select name="type" class="sf-select"><option value="">All types</option>
    @foreach(['purchase','sale','return','adjustment'] as $t)<option @selected(request('type')===$t)>{{ $t }}</option>@endforeach</select>
  <button class="sf-btn-secondary">Filter</button></form>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">Type</th><th class="py-2 pr-4">Qty</th><th class="py-2 pr-4">Stock after</th><th class="py-2 pr-4">Reference</th><th class="py-2">By</th></tr></thead>
<tbody>@forelse($movements as $m)
<tr class="border-b"><td class="py-2 pr-4">{{ $m->created_at }}</td><td class="py-2 pr-4">{{ $m->product?->name }}</td>
<td class="py-2 pr-4">{{ $m->type->value }}</td><td class="py-2 pr-4">{{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}</td>
<td class="py-2 pr-4">{{ $m->stock_after }}</td><td class="py-2 pr-4">{{ $m->reference_type ? class_basename($m->reference_type).' #'.$m->reference_id : '—' }}</td>
<td class="py-2">{{ $m->user?->name ?? '—' }}</td></tr>
@empty<tr><td colspan="7" class="py-4 text-center text-gray-500">No movements.</td></tr>@endforelse</tbody></table>
<div class="mt-4">{{ $movements->links() }}</div>
    </div></div></div>
</x-app-layout>
