<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Sales Returns</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Return no</th><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Invoice</th><th class="py-2 pr-4">Refund</th><th class="py-2 pr-4">Reason</th><th class="py-2">Processed by</th></tr></thead>
<tbody>@forelse($returns as $r)
<tr class="border-b"><td class="py-2 pr-4">{{ $r->return_no }}</td><td class="py-2 pr-4">{{ $r->return_date }}</td>
<td class="py-2 pr-4"><a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('sales.show',$r->sale) }}">{{ $r->sale?->invoice_no }}</a></td>
<td class="py-2 pr-4">{{ $r->total_refund }}</td><td class="py-2 pr-4">{{ $r->reason ?? '—' }}</td><td class="py-2">{{ $r->user?->name }}</td></tr>
@empty<tr><td colspan="6" class="py-4 text-center text-gray-500">No returns. Open a sale and use Process return.</td></tr>@endforelse</tbody></table>
<div class="mt-4">{{ $returns->links() }}</div>
    </div></div></div>
</x-app-layout>
