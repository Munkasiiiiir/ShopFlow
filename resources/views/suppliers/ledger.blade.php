<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Supplier Ledger</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<p class="text-sm mb-4"><b>{{ $supplier->name }}</b> — Total due: <b>{{ number_format($supplier->totalDue(),2) }}</b>
<a class="ml-2 text-blue-600" href="{{ route('suppliers.ledger.pdf', $supplier) }}">Download PDF</a></p>
<h3 class="font-semibold mb-2">Purchases</h3>
<table class="min-w-full text-sm mb-6"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Reference</th><th class="py-2 pr-4">Total</th><th class="py-2 pr-4">Paid</th><th class="py-2">Due</th></tr></thead>
<tbody>@foreach($supplier->purchases as $p)
<tr class="border-b"><td class="py-2 pr-4">{{ $p->purchase_date->format('Y-m-d') }}</td><td class="py-2 pr-4">{{ $p->reference_no }}</td>
<td class="py-2 pr-4">{{ $p->total_amount }}</td><td class="py-2 pr-4">{{ $p->paid_amount }}</td><td class="py-2">{{ $p->due_amount }}</td></tr>@endforeach</tbody></table>
<h3 class="font-semibold mb-2">Payments</h3>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Purchase</th><th class="py-2 pr-4">Amount</th><th class="py-2">Method</th></tr></thead>
<tbody>@foreach($supplier->payments as $pay)
<tr class="border-b"><td class="py-2 pr-4">{{ $pay->payment_date->format('Y-m-d') }}</td><td class="py-2 pr-4">{{ $pay->purchase?->reference_no }}</td>
<td class="py-2 pr-4">{{ $pay->amount }}</td><td class="py-2">{{ $pay->payment_method->value }}</td></tr>@endforeach</tbody></table>
    </div></div></div>
</x-app-layout>
