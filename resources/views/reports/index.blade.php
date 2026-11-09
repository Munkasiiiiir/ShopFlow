<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Reports</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<div class="grid gap-3 md:grid-cols-2 max-w-3xl text-sm">
  <a class="rounded border p-4 hover:bg-gray-50" href="{{ route('reports.sales') }}"><b>Sales Report</b><br>Daily/monthly sales with filters, PDF & CSV export</a>
  <a class="rounded border p-4 hover:bg-gray-50" href="{{ route('reports.top-products') }}"><b>Top-Selling Products</b><br>Best sellers by quantity and amount</a>
  <a class="rounded border p-4 hover:bg-gray-50" href="{{ route('reports.stock') }}"><b>Stock Valuation</b><br>Quantity and value of current stock</a>
  <a class="rounded border p-4 hover:bg-gray-50" href="{{ route('reports.profit') }}"><b>Profit Summary</b><br>Revenue minus cost of goods sold</a>
  <a class="rounded border p-4 hover:bg-gray-50" href="{{ route('reports.purchases') }}"><b>Purchase Report</b><br>Purchases by date range and supplier</a>
</div>
    </div></div></div>
</x-app-layout>
