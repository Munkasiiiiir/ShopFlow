<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Customer Details</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<div class="text-sm space-y-1">
  <p><b>{{ $customer->name }}</b> @if($customer->is_walk_in)<span class="text-gray-500">(walk-in)</span>@endif</p>
  <p>Phone: {{ $customer->phone ?? '—' }} &nbsp; Address: {{ $customer->address ?? '—' }} &nbsp; Status: {{ $customer->is_active ? 'Active' : 'Inactive' }}</p></div>
<h3 class="mt-6 mb-2 font-semibold">Purchase history</h3>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Invoice</th><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Total</th><th class="py-2">Status</th></tr></thead>
<tbody>@forelse($customer->sales as $s)
<tr class="border-b"><td class="py-2 pr-4"><a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('sales.show',$s) }}">{{ $s->invoice_no }}</a></td>
<td class="py-2 pr-4">{{ $s->sale_date }}</td><td class="py-2 pr-4">{{ $s->grand_total }}</td><td class="py-2">{{ $s->status->value }}</td></tr>
@empty<tr><td colspan="4" class="py-4 text-center text-gray-500">No purchases yet.</td></tr>@endforelse</tbody></table>
    </div></div></div>
</x-app-layout>
