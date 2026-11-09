<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">New Customers</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

    <form method="POST" action="{{ route('customers.store') }}" class="space-y-4 max-w-xl">@csrf
    <div><label class="block text-sm font-medium">Name</label><input type="text" name="name" value="" class="sf-input">@error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Phone</label><input type="text" name="phone" value="" class="sf-input">@error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Address</label><textarea name="address" class="sf-input" rows="2"></textarea>@error('address')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
    <button class="sf-btn-primary">Save</button> <a href="{{ route('customers.index') }}" class="text-sm text-gray-600">Cancel</a></form>
    </div></div></div>
</x-app-layout>
