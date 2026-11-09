<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="sf-page-title">Products</h2>
            <p class="sf-page-subtitle">Catalog, pricing, and stock levels</p>
        </div>
    </x-slot>

    <div class="p-4 md:p-6">
        <div class="sf-card">
            <div class="border-b border-slate-100 p-5 md:p-6">
                <x-flash />

                <form method="GET" class="flex flex-wrap items-end gap-2">
                    <div class="min-w-[12rem] flex-1">
                        <label class="mb-1 block text-xs font-medium text-ink-muted">Search</label>
                        <input name="q" value="{{ request('q') }}" placeholder="Name or SKU" class="sf-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-ink-muted">Category</label>
                        <select name="category_id" class="sf-select">
                            <option value="">All categories</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-ink-muted">Brand</label>
                        <select name="brand_id" class="sf-select">
                            <option value="">All brands</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" @selected(request('brand_id')==$b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-ink-muted">Stock</label>
                        <select name="stock_status" class="sf-select">
                            <option value="">All status</option>
                            <option value="in_stock" @selected(request('stock_status')==='in_stock')>In stock</option>
                            <option value="low_stock" @selected(request('stock_status')==='low_stock')>Low stock</option>
                            <option value="out_of_stock" @selected(request('stock_status')==='out_of_stock')>Out of stock</option>
                        </select>
                    </div>
                    <button class="sf-btn-secondary">Search</button>
                    @if(in_array(auth()->user()?->role->value, ['admin','manager']))
                        <a href="{{ route('products.create') }}" class="sf-btn-primary">+ New Product</a>
                    @endif
                    <a href="{{ route('products.low-stock') }}" class="sf-btn border border-amber-200 bg-amber-50 text-amber-800 hover:bg-amber-100">Low stock</a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="sf-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Cost</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 text-xs font-semibold text-slate-400">
                                            @if($p->image)
                                                <img src="{{ asset('storage/'.$p->image) }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                {{ strtoupper(substr($p->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('products.show', $p) }}">{{ $p->name }}</a>
                                    </div>
                                </td>
                                <td class="font-mono text-xs">{{ $p->sku }}</td>
                                <td>{{ $p->category?->name }}</td>
                                <td>{{ number_format($p->cost_price, 2) }}</td>
                                <td class="font-semibold text-ink-heading">{{ number_format($p->sale_price, 2) }}</td>
                                <td>
                                    @if($p->stock <= 0)
                                        <span class="sf-badge-danger">{{ $p->stock }} out</span>
                                    @elseif($p->isLowStock())
                                        <span class="sf-badge-warning">{{ $p->stock }} low</span>
                                    @else
                                        <span class="sf-badge-success">{{ $p->stock }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap">
                                    @if(in_array(auth()->user()?->role->value, ['admin','manager']))
                                        <a class="sf-btn-secondary !px-2.5 !py-1 text-xs" href="{{ route('products.edit', $p) }}">Edit</a>
                                        <form method="POST" action="{{ route('products.destroy', $p) }}" class="inline" onsubmit="return confirm('Delete?')">
                                            @csrf @method('DELETE')
                                            <button class="sf-btn-danger !px-2.5 !py-1 text-xs">Delete</button>
                                        </form>
                                    @else
                                        <a class="text-sm font-medium text-brand-700" href="{{ route('products.show', $p) }}">View</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-ink-muted">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
