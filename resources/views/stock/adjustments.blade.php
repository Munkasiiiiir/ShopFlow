<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Stock Adjustments</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="POST" action="{{ route('adjustments.store') }}" class="mb-6 grid gap-3 max-w-2xl md:grid-cols-2">@csrf
  <div><label class="block text-sm font-medium">Product</label>
  <select name="product_id" class="sf-input" required><option value="">Select product</option>
  @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} (stock: {{ $p->stock }})</option>@endforeach</select></div>
  <div><label class="block text-sm font-medium">Type</label>
  <select name="type" class="sf-input"><option value="damaged">Damaged</option><option value="lost">Lost</option><option value="correction">Correction</option></select></div>
  <div><label class="block text-sm font-medium">Quantity (+ add / - remove)</label><input type="number" name="quantity" value="" class="sf-input">@error('quantity')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
  <div><label class="block text-sm font-medium">Reason</label><input type="text" name="reason" value="" class="sf-input">@error('reason')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
  <div><button class="sf-btn-primary">Apply adjustment</button></div></form>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">Type</th><th class="py-2 pr-4">Qty</th><th class="py-2 pr-4">Reason</th><th class="py-2">By</th></tr></thead>
<tbody>@forelse($adjustments as $a)
<tr class="border-b"><td class="py-2 pr-4">{{ $a->created_at }}</td><td class="py-2 pr-4">{{ $a->product?->name }}</td>
<td class="py-2 pr-4">{{ $a->type->value }}</td><td class="py-2 pr-4">{{ $a->quantity }}</td>
<td class="py-2 pr-4">{{ $a->reason }}</td><td class="py-2">{{ $a->user?->name }}</td></tr>
@empty<tr><td colspan="6" class="py-4 text-center text-gray-500">No adjustments.</td></tr>@endforelse</tbody></table>
<div class="mt-4">{{ $adjustments->links() }}</div>
    </div></div></div>
</x-app-layout>
