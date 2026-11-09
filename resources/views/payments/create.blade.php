<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Record Supplier Payment</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<p class="text-sm mb-4">Purchase <b>{{ $purchase->reference_no }}</b> — Supplier: {{ $purchase->supplier?->name }},
Total: {{ $purchase->total_amount }}, Paid: {{ $purchase->paid_amount }}, <b>Due: {{ $purchase->due_amount }}</b></p>
<form method="POST" action="{{ route('payments.store', $purchase) }}" class="space-y-4 max-w-xl">@csrf
<div><label class="block text-sm font-medium">Amount</label><input type="number" name="amount" value="" class="sf-input">@error('amount')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Payment date</label><input type="date" name="payment_date" value="{{ now()->toDateString() }}" class="sf-input">@error('payment_date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Payment method</label>
<select name="payment_method" class="sf-input"><option value="cash">Cash</option><option value="bank">Bank</option><option value="mobile_banking">Mobile Banking</option></select></div>
<div><label class="block text-sm font-medium">Note</label><input type="text" name="note" value="" class="sf-input">@error('note')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<button class="sf-btn-primary">Record payment</button></form>
    </div></div></div>
</x-app-layout>
