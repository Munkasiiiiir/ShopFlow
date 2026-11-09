<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Product Details</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<div class="grid gap-2 max-w-2xl text-sm">
  <p><b>Name:</b> {{ $product->name }} &nbsp; <b>SKU:</b> {{ $product->sku }}</p>
  <p><b>Category:</b> {{ $product->category?->name }} &nbsp; <b>Brand:</b> {{ $product->brand?->name ?? '—' }} &nbsp; <b>Unit:</b> {{ $product->unit?->name }}</p>
  <p><b>Cost price:</b> {{ $product->cost_price }} &nbsp; <b>Sale price:</b> {{ $product->sale_price }}</p>
  <p><b>Current stock:</b> {{ $product->stock }} (alert at {{ $product->alert_qty }}) — {{ str_replace('_',' ',$product->stockStatus()) }}</p>
  @if($product->image)<img src="{{ asset('storage/'.$product->image) }}" class="h-32 rounded" alt="">@endif
  <p>{{ $product->description }}</p></div>
<h3 class="mt-6 mb-2 font-semibold">Recent stock movements</h3>
<table class="min-w-full text-sm max-w-2xl"><tbody>
@foreach($movements as $m)<tr class="border-b"><td class="py-1 pr-4">{{ $m->created_at }}</td><td class="py-1 pr-4">{{ $m->type->value }}</td><td class="py-1 pr-4">{{ $m->quantity }}</td><td class="py-1">stock after: {{ $m->stock_after }}</td></tr>@endforeach
</tbody></table>
<div class="mt-4"><a class="text-sm font-medium text-brand-700 hover:text-brand-600" href="{{ route('products.index') }}">Back to products</a></div>
    </div></div></div>
</x-app-layout>
