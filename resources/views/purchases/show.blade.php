<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Purchase Details</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<div class="text-sm space-y-1 max-w-3xl">
  <p><b>Reference:</b> {{ $purchase->reference_no }} &nbsp; <b>Date:</b> {{ $purchase->purchase_date->format('Y-m-d') }}</p>
  <p><b>Supplier:</b> {{ $purchase->supplier?->name }} &nbsp; <b>Created by:</b> {{ $purchase->user?->name }}</p>
  <p><b>Total:</b> {{ $purchase->total_amount }} &nbsp; <b>Paid:</b> {{ $purchase->paid_amount }} &nbsp; <b>Due:</b> {{ $purchase->due_amount }}
  @if($purchase->due_amount > 0)<a class="ml-2 rounded bg-green-600 px-2 py-1 text-white text-xs" href="{{ route('payments.create', $purchase) }}">Record payment</a>@endif</p>
</div>
<table class="min-w-full text-sm mt-4 max-w-3xl"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">Qty</th><th class="py-2 pr-4">Unit cost</th><th class="py-2">Subtotal</th></tr></thead>
<tbody>@foreach($purchase->items as $i)
<tr class="border-b"><td class="py-2 pr-4">{{ $i->product?->name }}</td><td class="py-2 pr-4">{{ $i->quantity }}</td><td class="py-2 pr-4">{{ $i->unit_cost }}</td><td class="py-2">{{ $i->subtotal }}</td></tr>@endforeach</tbody></table>
@if($purchase->payments->count())
<h3 class="mt-6 mb-2 font-semibold">Payments</h3>
<table class="min-w-full text-sm max-w-3xl"><tbody>
@foreach($purchase->payments as $pay)<tr class="border-b"><td class="py-1 pr-4">{{ $pay->payment_date->format('Y-m-d') }}</td><td class="py-1 pr-4">{{ $pay->amount }}</td><td class="py-1 pr-4">{{ $pay->payment_method->value }}</td><td class="py-1">{{ $pay->user?->name }}</td></tr>@endforeach
</tbody></table>@endif
    </div></div></div>
</x-app-layout>
