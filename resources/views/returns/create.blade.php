<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Process Sales Return</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<p class="text-sm mb-4">Invoice <b>{{ $sale->invoice_no }}</b> — Customer: {{ $sale->customer?->name }}, Total: {{ $sale->grand_total }}</p>
<form method="POST" action="{{ route('returns.store', $sale) }}" class="space-y-4 max-w-3xl">@csrf
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">Sold</th><th class="py-2 pr-4">Returned</th><th class="py-2 pr-4">Returnable</th><th class="py-2">Return qty</th></tr></thead>
<tbody>@foreach($sale->items as $i => $item)
<tr class="border-b"><td class="py-2 pr-4">{{ $item->product?->name }}</td><td class="py-2 pr-4">{{ $item->quantity }}</td>
<td class="py-2 pr-4">{{ $item->returned_qty }}</td><td class="py-2 pr-4">{{ $item->returnableQty() }}</td>
<td class="py-2"><input type="hidden" name="items[{{ $i }}][sale_item_id]" value="{{ $item->id }}">
<input type="number" name="items[{{ $i }}][quantity]" value="0" min="0" max="{{ $item->returnableQty() }}" class="rounded border-gray-300 w-24 text-sm"></td></tr>@endforeach</tbody></table>
<div><label class="block text-sm font-medium">Reason</label><input name="reason" class="sf-input"></div>
<button class="sf-btn-primary">Process return (adds stock back)</button></form>
    </div></div></div>
</x-app-layout>
