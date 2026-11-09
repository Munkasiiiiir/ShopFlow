<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Purchase Report</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
  <label class="text-sm">From <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="sf-select block"></label>
  <label class="text-sm">To <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="sf-select block"></label>
  <label class="text-sm">Supplier <select name="supplier_id" class="sf-select block"><option value="">All</option>
    @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(($filters['supplier_id'] ?? '')==$s->id)>{{ $s->name }}</option>@endforeach</select></label>
  <button class="sf-btn-secondary">Filter</button></form>
<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Reference</th><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Supplier</th><th class="py-2 pr-4">Total</th><th class="py-2 pr-4">Paid</th><th class="py-2">Due</th></tr></thead>
<tbody>@forelse($purchases as $p)
<tr class="border-b"><td class="py-2 pr-4">{{ $p->reference_no }}</td><td class="py-2 pr-4">{{ $p->purchase_date->format('Y-m-d') }}</td>
<td class="py-2 pr-4">{{ $p->supplier?->name }}</td><td class="py-2 pr-4">{{ $p->total_amount }}</td>
<td class="py-2 pr-4">{{ $p->paid_amount }}</td><td class="py-2">{{ $p->due_amount }}</td></tr>
@empty<tr><td colspan="6" class="py-4 text-center text-gray-500">No purchases.</td></tr>@endforelse</tbody></table>
<p class="mt-3 font-semibold">Totals — Purchased: {{ number_format($totals->t ?? 0,2) }}, Paid: {{ number_format($totals->p ?? 0,2) }}, Due: {{ number_format($totals->d ?? 0,2) }}</p>
<div class="mt-2">{{ $purchases->links() }}</div>
    </div></div></div>
</x-app-layout>
