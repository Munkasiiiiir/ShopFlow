<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="sf-page-title">Point of Sale</h2>
            <p class="sf-page-subtitle">Search products, build the cart, and check out</p>
        </div>
    </x-slot>

    <div class="p-4 md:p-6" x-data="pos()">
        <x-flash />

        <form method="POST" action="{{ route('pos.checkout') }}" class="grid gap-4 xl:grid-cols-12" @submit="if (cart.length === 0) $event.preventDefault()">
            @csrf

            <div class="space-y-4 xl:col-span-7">
                <div class="sf-card p-4 md:p-5">
                    <label class="mb-2 block text-sm font-semibold text-ink-heading">Search product</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                        <input x-model="query" @input.debounce.300ms="search()" class="sf-input pl-10" placeholder="Type name or SKU..." autofocus>
                    </div>

                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        <template x-for="p in results" :key="p.id">
                            <button type="button" @click="add(p)" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 text-left transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-soft">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 text-sm font-bold text-slate-400">
                                    <template x-if="p.image_url">
                                        <img :src="p.image_url" alt="" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!p.image_url">
                                        <span x-text="p.name.charAt(0).toUpperCase()"></span>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold text-ink-heading" x-text="p.name"></div>
                                    <div class="text-xs text-ink-muted"><span x-text="p.sku"></span> · stock <span x-text="p.stock"></span></div>
                                </div>
                                <div class="text-sm font-bold text-brand-700" x-text="Number(p.sale_price).toFixed(2)"></div>
                            </button>
                        </template>
                    </div>
                    <p x-show="query.length > 0 && results.length === 0" class="mt-4 text-sm text-ink-muted">No products matched that search.</p>
                    <p x-show="query.length === 0" class="mt-4 text-sm text-ink-muted">Start typing to find products and add them to the cart.</p>
                </div>
            </div>

            <div class="xl:col-span-5">
                <div class="sf-card sticky top-20 overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink-heading">Cart</h3>
                        <p class="text-xs text-ink-muted"><span x-text="cart.length"></span> item(s)</p>
                    </div>

                    <div class="max-h-[20rem] overflow-y-auto px-5 py-3">
                        <template x-for="(item, i) in cart" :key="item.id">
                            <div class="flex items-center gap-3 border-b border-slate-100 py-3 last:border-0">
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold text-ink-heading" x-text="item.name"></div>
                                    <div class="text-xs text-ink-muted" x-text="item.price.toFixed(2) + ' each'"></div>
                                    <input type="hidden" :name="`items[${i}][product_id]`" :value="item.id">
                                </div>
                                <div class="flex items-center rounded-lg border border-slate-200">
                                    <button type="button" class="px-2 py-1 text-slate-500 hover:bg-slate-50" @click="item.qty = Math.max(1, item.qty - 1)">−</button>
                                    <input type="number" min="1" :max="item.stock" :name="`items[${i}][quantity]`" x-model.number="item.qty" class="w-12 border-0 bg-transparent p-0 text-center text-sm focus:ring-0">
                                    <button type="button" class="px-2 py-1 text-slate-500 hover:bg-slate-50" @click="if (item.qty < item.stock) item.qty++">+</button>
                                </div>
                                <div class="w-16 text-right text-sm font-semibold" x-text="(item.price * item.qty).toFixed(2)"></div>
                                <button type="button" class="rounded-md p-1 text-red-500 hover:bg-red-50" @click="cart.splice(i,1)" aria-label="Remove">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <p x-show="cart.length === 0" class="py-8 text-center text-sm text-ink-muted">Cart is empty. Search and add products.</p>
                    </div>

                    <div class="space-y-3 border-t border-slate-100 px-5 py-4 text-sm">
                        <div class="flex justify-between text-ink-muted"><span>Subtotal</span><b class="text-ink-heading" x-text="subtotal.toFixed(2)"></b></div>
                        <label class="flex items-center justify-between gap-3">
                            <span class="text-ink-muted">Discount</span>
                            <input type="number" step="0.01" min="0" name="discount" x-model.number="discount" class="sf-input w-28">
                        </label>
                        <label class="flex items-center justify-between gap-3">
                            <span class="text-ink-muted">Tax</span>
                            <input type="number" step="0.01" min="0" name="tax" x-model.number="tax" class="sf-input w-28">
                        </label>
                        <div class="flex justify-between border-t border-dashed border-slate-200 pt-3 text-base">
                            <span class="font-semibold text-ink-heading">Grand total</span>
                            <b class="text-lg text-brand-700" x-text="grand.toFixed(2)"></b>
                        </div>

                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-ink-muted">Customer</span>
                            <select name="customer_id" class="sf-select w-full" required>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}{{ $c->is_walk_in ? ' (default)' : '' }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-ink-muted">Payment method</span>
                            <select name="payment_method" x-model="method" class="sf-select w-full">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile_banking">Mobile Banking</option>
                            </select>
                        </label>

                        <label class="flex items-center justify-between gap-3">
                            <span class="text-ink-muted">Paid amount</span>
                            <input type="number" step="0.01" min="0" name="paid_amount" x-model.number="paid" class="sf-input w-28" required>
                        </label>
                        <div class="flex justify-between text-ink-muted"><span>Change</span><b class="text-emerald-700" x-text="Math.max(paid - grand, 0).toFixed(2)"></b></div>

                        <button type="submit" class="sf-btn-accent w-full py-3 text-base" :disabled="cart.length===0">Complete Sale</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
    function pos() {
        return {
            query: '',
            results: [],
            cart: [],
            discount: 0,
            tax: 0,
            paid: 0,
            method: 'cash',
            async search() {
                if (this.query.length < 1) { this.results = []; return; }
                const res = await fetch('{{ route('pos.products') }}?q=' + encodeURIComponent(this.query));
                this.results = await res.json();
            },
            add(p) {
                const found = this.cart.find(i => i.id === p.id);
                if (found) {
                    if (found.qty < found.stock) found.qty++;
                } else {
                    this.cart.push({ id: p.id, name: p.name, price: Number(p.sale_price), stock: p.stock, qty: 1 });
                }
            },
            get subtotal() { return this.cart.reduce((s, i) => s + i.price * i.qty, 0); },
            get grand() { return this.subtotal - Number(this.discount || 0) + Number(this.tax || 0); }
        };
    }
    </script>
</x-app-layout>
