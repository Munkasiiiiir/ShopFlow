<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Shop Settings</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

<form method="POST" action="{{ route('settings.update') }}" class="space-y-4 max-w-xl">@csrf @method('PATCH')
<div><label class="block text-sm font-medium">Shop name</label><input type="text" name="shop_name" value="{{ old('shop_name', $settings['shop_name'] ?? '') }}" class="sf-input">@error('shop_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Address</label><input type="text" name="shop_address" value="{{ old('shop_address', $settings['shop_address'] ?? '') }}" class="sf-input">@error('shop_address')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Phone</label><input type="text" name="shop_phone" value="{{ old('shop_phone', $settings['shop_phone'] ?? '') }}" class="sf-input">@error('shop_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Currency</label><input type="text" name="currency" value="{{ old('currency', $settings['currency'] ?? 'BDT') }}" class="sf-input">@error('currency')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Tax rate (%)</label><input type="number" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate'] ?? 0) }}" class="sf-input">@error('tax_rate')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Invoice prefix</label><input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-') }}" class="sf-input">@error('invoice_prefix')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<button class="sf-btn-primary">Save settings</button></form>
    </div></div></div>
</x-app-layout>
