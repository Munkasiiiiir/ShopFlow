<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Edit Suppliers</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4 max-w-xl">@csrf @method('PUT')
    <div><label class="block text-sm font-medium">Contact name</label><input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="sf-input">@error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Company</label><input type="text" name="company" value="{{ old('company', $supplier->company) }}" class="sf-input">@error('company')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Phone</label><input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="sf-input">@error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Email</label><input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="sf-input">@error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Address</label><textarea name="address" class="sf-input" rows="2">{{ old('address', $supplier->address) }}</textarea>@error('address')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
    <button class="sf-btn-primary">Update</button> <a href="{{ route('suppliers.index') }}" class="text-sm text-gray-600">Cancel</a></form>
    </div></div></div>
</x-app-layout>
