<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Current Stock</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="GET" class="mb-4 flex flex-wrap gap-2">
  <input name="q" value="{{ request('q') }}" placeholder="Search product" class="sf-select">
  <select name="category_id" class="sf-select"><option value="">All categories</option>
    @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>@endforeach</select>
  <select name="stock_status" class="sf-select"><option value="">All status</option>
    <option value="in_stock" @selected(request('stock_status')==='in_stock')>In stock</option>
    <option value="low_stock" @selected(request('stock_status')==='low_stock')>Low stock</option>
    <option value="out_of_stock" @selected(request('stock_status')==='out_of_stock')>Out of stock</option></select>
  <button class="sf-btn-secondary">Filter</button></form>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">SKU</th><th class="py-2 pr-4">Category</th><th class="py-2 pr-4">Stock</th><th class="py-2 pr-4">Alert</th><th class="py-2">Status</th></tr></thead>
<tbody>@forelse($products as $p)
<tr class="border-b"><td class="py-2 pr-4">{{ $p->name }}</td><td class="py-2 pr-4">{{ $p->sku }}</td>
<td class="py-2 pr-4">{{ $p->category?->name }}</td><td class="py-2 pr-4">{{ $p->stock }} {{ $p->unit?->short_name }}</td>
<td class="py-2 pr-4">{{ $p->alert_qty }}</td>
<td class="py-2">@php($s = $p->stockStatus())
<span class="rounded px-2 py-0.5 {{ $s==='in_stock' ? 'bg-green-100 text-green-800' : ($s==='low_stock' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ str_replace('_',' ',$s) }}</span></td></tr>
@empty<tr><td colspan="6" class="py-4 text-center text-gray-500">No products.</td></tr>@endforelse</tbody></table>
<div class="mt-4">{{ $products->links() }}</div>
    </div></div></div>
</x-app-layout>
