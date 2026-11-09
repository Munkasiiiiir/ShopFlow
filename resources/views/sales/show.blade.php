<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Sale / Invoice</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<div class="text-sm space-y-1 max-w-3xl">
  <p><b>Invoice:</b> {{ $sale->invoice_no }} &nbsp; <b>Date:</b> {{ $sale->sale_date }}</p>
  <p><b>Customer:</b> {{ $sale->customer?->name }} &nbsp; <b>Cashier:</b> {{ $sale->user?->name }} &nbsp; <b>Status:</b> {{ $sale->status->value }}</p>
  <p><b>Payment:</b> {{ $sale->payment_method->value }} &nbsp; Paid: {{ $sale->paid_amount }} &nbsp; Change: {{ $sale->change_amount }}</p>
</div>
<table class="min-w-full text-sm mt-4 max-w-3xl"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">Qty</th><th class="py-2 pr-4">Unit price</th><th class="py-2 pr-4">Returned</th><th class="py-2">Subtotal</th></tr></thead>
<tbody>@foreach($sale->items as $i)
<tr class="border-b"><td class="py-2 pr-4">{{ $i->product?->name }}</td><td class="py-2 pr-4">{{ $i->quantity }}</td>
<td class="py-2 pr-4">{{ $i->unit_price }}</td><td class="py-2 pr-4">{{ $i->returned_qty }}</td><td class="py-2">{{ $i->subtotal }}</td></tr>@endforeach</tbody></table>
<div class="text-sm mt-3 max-w-3xl space-y-1">
  <p class="flex justify-between max-w-xs"><span>Subtotal</span><span>{{ $sale->subtotal }}</span></p>
  <p class="flex justify-between max-w-xs"><span>Discount</span><span>- {{ $sale->discount }}</span></p>
  <p class="flex justify-between max-w-xs"><span>Tax</span><span>+ {{ $sale->tax }}</span></p>
  <p class="flex justify-between max-w-xs font-bold"><span>Grand total</span><span>{{ $sale->grand_total }}</span></p>
</div>
<div class="mt-4 flex gap-2">
  <a class="rounded bg-gray-800 px-3 py-1.5 text-white text-sm" href="{{ route('sales.invoice.pdf', $sale) }}">Download PDF invoice</a>
  @if(auth()->user()->role->value !== 'cashier')
  <a class="rounded bg-yellow-600 px-3 py-1.5 text-white text-sm" href="{{ route('returns.create', $sale) }}">Process return</a>@endif
</div>
@if($sale->returns->count())
<h3 class="mt-6 mb-2 font-semibold">Returns</h3>
<table class="min-w-full text-sm max-w-3xl"><tbody>
@foreach($sale->returns as $r)<tr class="border-b"><td class="py-1 pr-4">{{ $r->return_no }}</td><td class="py-1 pr-4">{{ $r->return_date }}</td><td class="py-1">Refund: {{ $r->total_refund }}</td></tr>@endforeach
</tbody></table>@endif
    </div></div></div>
</x-app-layout>
