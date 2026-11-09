<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">New Purchase</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="POST" action="{{ route('purchases.store') }}" x-data="purchaseForm()" class="space-y-4 max-w-4xl">@csrf
  <div class="grid gap-4 md:grid-cols-3">
    <div><label class="block text-sm font-medium">Supplier</label>
      <select name="supplier_id" class="sf-input" required>
      <option value="">Select supplier</option>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}{{ $s->company ? ' ('.$s->company.')' : '' }}</option>@endforeach</select></div>
    <div><label class="block text-sm font-medium">Purchase date</label>
      <input type="date" name="purchase_date" value="{{ now()->toDateString() }}" class="sf-input" required></div>
    <div><label class="block text-sm font-medium">Paid amount</label>
      <input type="number" step="0.01" min="0" name="paid_amount" value="0" class="sf-input"></div>
  </div>
  <table class="sf-table"><thead><tr class="border-b text-left"><th class="py-2 pr-4">Product</th><th class="py-2 pr-4">Qty</th><th class="py-2 pr-4">Unit cost</th><th class="py-2 pr-4">Subtotal</th><th></th></tr></thead>
  <tbody>
    <template x-for="(row, i) in rows" :key="i">
      <tr class="border-b">
        <td class="py-2 pr-4"><select :name="`items[${i}][product_id]`" x-model="row.product_id" class="sf-select" required>
          <option value="">Select product</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>@endforeach</select></td>
        <td class="py-2 pr-4"><input type="number" min="1" :name="`items[${i}][quantity]`" x-model.number="row.quantity" class="rounded border-gray-300 w-24 text-sm" required></td>
        <td class="py-2 pr-4"><input type="number" step="0.01" min="0" :name="`items[${i}][unit_cost]`" x-model.number="row.unit_cost" class="rounded border-gray-300 w-28 text-sm" required></td>
        <td class="py-2 pr-4" x-text="(row.quantity * row.unit_cost || 0).toFixed(2)"></td>
        <td class="py-2"><button type="button" class="text-red-600" @click="rows.splice(i,1)">Remove</button></td>
      </tr>
    </template>
  </tbody></table>
  <div class="flex items-center gap-4">
    <button type="button" class="sf-btn-secondary" @click="rows.push({product_id:'',quantity:1,unit_cost:0})">+ Add product</button>
    <span class="font-semibold">Total: <span x-text="total.toFixed(2)"></span></span>
  </div>
  <div><label class="block text-sm font-medium">Note</label><input name="note" class="sf-input"></div>
  <button class="sf-btn-primary">Save purchase (adds stock)</button>
</form>
<script>
function purchaseForm() {
  return { rows: [{product_id:'',quantity:1,unit_cost:0}],
    get total() { return this.rows.reduce((s,r) => s + (r.quantity * r.unit_cost || 0), 0); } };
}
</script>
    </div></div></div>
</x-app-layout>
