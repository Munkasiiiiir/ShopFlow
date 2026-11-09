<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="sf-page-title">Sales</h2>
            <p class="sf-page-subtitle">Invoices and checkout history</p>
        </div>
    </x-slot>

    <div class="p-4 md:p-6">
        <div class="sf-card">
            <div class="border-b border-slate-100 p-5 md:p-6">
                <x-flash />
                <form method="GET" class="flex flex-wrap items-end gap-2">
                    <div class="min-w-[12rem] flex-1">
                        <label class="mb-1 block text-xs font-medium text-ink-muted">Invoice</label>
                        <input name="q" value="{{ request('q') }}" placeholder="Invoice no" class="sf-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-ink-muted">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="sf-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-ink-muted">Cashier</label>
                        <select name="user_id" class="sf-select">
                            <option value="">All cashiers</option>
                            @foreach($cashiers as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="sf-btn-secondary">Search</button>
                    <a href="{{ route('pos.index') }}" class="sf-btn-accent">New Sale (POS)</a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="sf-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Cashier</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $s)
                            <tr>
                                <td><a class="font-semibold text-brand-700 hover:text-brand-600" href="{{ route('sales.show', $s) }}">{{ $s->invoice_no }}</a></td>
                                <td>{{ $s->sale_date }}</td>
                                <td>{{ $s->customer?->name }}</td>
                                <td>{{ $s->user?->name }}</td>
                                <td><span class="sf-badge-neutral">{{ $s->payment_method->value }}</span></td>
                                <td class="font-semibold">{{ number_format($s->grand_total, 2) }}</td>
                                <td>
                                    @php $st = $s->status->value; @endphp
                                    @if($st === 'completed')
                                        <span class="sf-badge-success">{{ $st }}</span>
                                    @elseif($st === 'returned' || $st === 'partially_returned')
                                        <span class="sf-badge-warning">{{ $st }}</span>
                                    @else
                                        <span class="sf-badge-neutral">{{ $st }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-10 text-center text-ink-muted">No sales.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $sales->links() }}</div>
        </div>
    </div>
</x-app-layout>
