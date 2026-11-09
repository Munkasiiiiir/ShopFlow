<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">New Product</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />
<form method="POST" enctype="multipart/form-data" action="{{ route('products.store') }}">
@csrf
<div class="grid gap-4 md:grid-cols-2 max-w-3xl">
<div><label class="block text-sm font-medium">Name</label><input type="text" name="name" value="{{ old('name') }}" class="sf-input">@error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">SKU (blank = auto-generate)</label><input type="text" name="sku" value="{{ old('sku') }}" class="sf-input">@error('sku')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Category</label><select name="category_id" class="sf-input" required>
  @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id ?? '')==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
<div><label class="block text-sm font-medium">Brand</label><select name="brand_id" class="sf-input">
  <option value="">No brand</option>@foreach($brands as $b)<option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id ?? '')==$b->id)>{{ $b->name }}</option>@endforeach</select></div>
<div><label class="block text-sm font-medium">Unit</label><select name="unit_id" class="sf-input" required>
  @foreach($units as $u)<option value="{{ $u->id }}" @selected(old('unit_id', $product->unit_id ?? '')==$u->id)>{{ $u->name }} ({{ $u->short_name }})</option>@endforeach</select></div>
<div><label class="block text-sm font-medium">Cost price</label><input type="number" name="cost_price" value="{{ old('cost_price') }}" class="sf-input">@error('cost_price')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Sale price</label><input type="number" name="sale_price" value="{{ old('sale_price') }}" class="sf-input">@error('sale_price')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Alert qty</label><input type="number" name="alert_qty" value="{{ old('alert_qty', 5) }}" class="sf-input">@error('alert_qty')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Image (max 2MB)</label><input type="file" name="image" accept="image/*" class="sf-input"></div>
<div><label class="block text-sm font-medium">Description</label><textarea name="description" class="sf-input" rows="2">{{ old('description', $product->description ?? '') }}</textarea></div>
<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> Active (available for sale)</label>
</div>
<div class="mt-4"><button class="sf-btn-primary">Save</button> <a href="{{ route('products.index') }}" class="text-sm text-gray-600">Cancel</a></div>
</form>    </div></div></div></div>
</x-app-layout>
