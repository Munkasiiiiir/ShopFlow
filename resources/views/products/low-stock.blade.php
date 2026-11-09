<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Low-Stock Products</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">SKU</th><th class="py-2 pr-4">Category</th><th class="py-2 pr-4">Stock</th><th class="py-2">Alert qty</th></tr></thead>
<tbody>@forelse($products as $p)
<tr class="border-b"><td class="py-2 pr-4">{{ $p->name }}</td><td class="py-2 pr-4">{{ $p->sku }}</td>
<td class="py-2 pr-4">{{ $p->category?->name }}</td><td class="py-2 pr-4 text-red-600 font-semibold">{{ $p->stock }}</td><td class="py-2">{{ $p->alert_qty }}</td></tr>
@empty<tr><td colspan="5" class="py-4 text-center text-gray-500">No low-stock products.</td></tr>@endforelse</tbody></table>
<div class="mt-4">{{ $products->links() }}</div>
    </div></div></div>
</x-app-layout>
