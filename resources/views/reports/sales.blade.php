<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Sales Report</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
  <label class="text-sm">From <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="sf-select block"></label>
  <label class="text-sm">To <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="sf-select block"></label>
  <label class="text-sm">Cashier <select name="user_id" class="sf-select block"><option value="">All</option>
    @foreach($cashiers as $u)<option value="{{ $u->id }}" @selected(($filters['user_id'] ?? '')==$u->id)>{{ $u->name }}</option>@endforeach</select></label>
  <label class="text-sm">Payment <select name="payment_method" class="sf-select block"><option value="">All</option>
    @foreach(['cash','card','mobile_banking'] as $m)<option @selected(($filters['payment_method'] ?? '')===$m)>{{ $m }}</option>@endforeach</select></label>
  <button class="sf-btn-secondary">Filter</button>
  <a class="rounded bg-red-600 px-3 py-1.5 text-white text-sm" href="{{ route('reports.sales.pdf', $filters) }}">PDF</a>
  <a class="sf-btn-accent" href="{{ route('reports.sales.csv', $filters) }}">CSV (Excel)</a></form>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Invoice</th><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Customer</th><th class="py-2 pr-4">Cashier</th><th class="py-2 pr-4">Payment</th><th class="py-2">Total</th></tr></thead>
<tbody>@forelse($sales as $s)
<tr class="border-b"><td class="py-2 pr-4">{{ $s->invoice_no }}</td><td class="py-2 pr-4">{{ $s->sale_date }}</td>
<td class="py-2 pr-4">{{ $s->customer?->name }}</td><td class="py-2 pr-4">{{ $s->user?->name }}</td>
<td class="py-2 pr-4">{{ $s->payment_method->value }}</td><td class="py-2">{{ $s->grand_total }}</td></tr>
@empty<tr><td colspan="6" class="py-4 text-center text-gray-500">No sales in this period.</td></tr>@endforelse</tbody></table>
<p class="mt-3 font-semibold">Total (all filtered): {{ number_format($total,2) }}</p>
<div class="mt-2">{{ $sales->links() }}</div>
    </div></div></div>
</x-app-layout>
