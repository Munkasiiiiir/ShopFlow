<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="sf-page-title">Dashboard</h2>
            <p class="sf-page-subtitle">{{ auth()->user()->name }} · {{ auth()->user()->role->value }}</p>
        </div>
    </x-slot>

    <div class="space-y-6 p-4 md:p-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="sf-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-ink-muted">Today's sales</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-ink-heading">{{ number_format($stats['today_sales'], 2) }}</p>
                        <p class="mt-1 text-xs text-ink-muted">{{ $stats['today_count'] }} sale(s) today</p>
                    </div>
                    <div class="sf-kpi-icon bg-brand-50 text-brand-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
            <div class="sf-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-ink-muted">Total revenue</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-ink-heading">{{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <div class="sf-kpi-icon bg-emerald-50 text-emerald-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
            </div>
            <div class="sf-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-ink-muted">Total products</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-ink-heading">{{ $stats['total_products'] }}</p>
                    </div>
                    <div class="sf-kpi-icon bg-sky-50 text-sky-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
            </div>
            <div class="sf-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-ink-muted">Low-stock products</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-amber-600">{{ $stats['low_stock_count'] }}</p>
                        <a class="mt-1 inline-block text-xs font-semibold text-brand-700 hover:text-brand-600" href="{{ route('products.low-stock') }}">View list →</a>
                    </div>
                    <div class="sf-kpi-icon bg-amber-50 text-amber-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="sf-card p-5">
                <h3 class="mb-4 text-sm font-semibold text-ink-heading">Sales — last 7 days</h3>
                <canvas id="dailyChart" height="180"></canvas>
            </div>
            <div class="sf-card p-5">
                <h3 class="mb-4 text-sm font-semibold text-ink-heading">Monthly sales — {{ now()->year }}</h3>
                <canvas id="monthlyChart" height="180"></canvas>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="sf-card p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-ink-heading">Top 5 best-selling products</h3>
                </div>
                <table class="sf-table">
                    <tbody>
                        @forelse($topProducts as $p)
                            <tr>
                                <td class="font-medium text-ink-heading">{{ $p->name }}</td>
                                <td class="text-right"><span class="sf-badge-info">{{ $p->qty }} sold</span></td>
                            </tr>
                        @empty
                            <tr><td class="py-4 text-ink-muted">No sales yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="sf-card p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-ink-heading">Recent sales</h3>
                    <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-600">All sales</a>
                </div>
                <table class="sf-table">
                    <tbody>
                        @forelse($recentSales as $s)
                            <tr>
                                <td>
                                    <a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('sales.show', $s) }}">{{ $s->invoice_no }}</a>
                                    <div class="text-xs text-ink-muted">{{ $s->customer?->name }}</div>
                                </td>
                                <td class="text-right font-semibold text-ink-heading">{{ number_format($s->grand_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-4 text-ink-muted">No sales yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($lowStockProducts) && $lowStockProducts->isNotEmpty())
            <div class="sf-card p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-ink-heading">Low stock alerts</h3>
                    <a href="{{ route('products.low-stock') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-600">View all</a>
                </div>
                <table class="sf-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Stock</th>
                            <th>Alert at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $p)
                            <tr>
                                <td class="font-medium"><a class="text-brand-700 hover:text-brand-600" href="{{ route('products.show', $p) }}">{{ $p->name }}</a></td>
                                <td>{{ $p->sku }}</td>
                                <td>
                                    @if($p->stock <= 0)
                                        <span class="sf-badge-danger">{{ $p->stock }}</span>
                                    @else
                                        <span class="sf-badge-warning">{{ $p->stock }}</span>
                                    @endif
                                </td>
                                <td>{{ $p->alert_qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        const daily = @json($daily), monthly = @json($monthly);
        new Chart(document.getElementById('dailyChart'), {
            type: 'bar',
            data: {
                labels: daily.map(d => d.day),
                datasets: [{
                    label: 'Sales',
                    data: daily.map(d => d.total),
                    backgroundColor: '#0D9488',
                    borderRadius: 8,
                    maxBarThickness: 36,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#E5E7EB' }, beginAtZero: true }
                }
            }
        });
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: monthly.map(m => m.month),
                datasets: [{
                    label: 'Sales',
                    data: monthly.map(m => m.total),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#059669',
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#E5E7EB' }, beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>
