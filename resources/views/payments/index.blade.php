<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Supplier Payments</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Supplier</th><th class="py-2 pr-4">Purchase</th><th class="py-2 pr-4">Amount</th><th class="py-2 pr-4">Method</th><th class="py-2">Recorded by</th></tr></thead>
<tbody>@forelse($payments as $pay)
<tr class="border-b"><td class="py-2 pr-4">{{ $pay->payment_date->format('Y-m-d') }}</td><td class="py-2 pr-4">{{ $pay->supplier?->name }}</td>
<td class="py-2 pr-4"><a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('purchases.show',$pay->purchase) }}">{{ $pay->purchase?->reference_no }}</a></td>
<td class="py-2 pr-4">{{ $pay->amount }}</td><td class="py-2 pr-4">{{ $pay->payment_method->value }}</td><td class="py-2">{{ $pay->user?->name }}</td></tr>
@empty<tr><td colspan="6" class="py-4 text-center text-gray-500">No payments.</td></tr>@endforelse</tbody></table>
<div class="mt-4">{{ $payments->links() }}</div>
    </div></div></div>
</x-app-layout>
