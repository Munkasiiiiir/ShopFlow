<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Profit Summary</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="GET" class="mb-4 flex gap-2 items-end">
  <label class="text-sm">From <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="sf-select block"></label>
  <label class="text-sm">To <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="sf-select block"></label>
  <button class="sf-btn-secondary">Filter</button></form>
<div class="grid gap-3 md:grid-cols-3 max-w-3xl">
  <div class="rounded border p-4"><p class="text-sm text-gray-500">Revenue</p><p class="text-xl font-bold">{{ number_format($summary['revenue'],2) }}</p></div>
  <div class="rounded border p-4"><p class="text-sm text-gray-500">Cost of goods sold</p><p class="text-xl font-bold">{{ number_format($summary['cogs'],2) }}</p></div>
  <div class="rounded border p-4 bg-green-50"><p class="text-sm text-gray-500">Profit</p><p class="text-xl font-bold text-green-700">{{ number_format($summary['profit'],2) }}</p></div>
</div>
<p class="text-xs text-gray-500 mt-3">Calculated from price/cost snapshots stored on each sale item (returns excluded).</p>
    </div></div></div>
</x-app-layout>
