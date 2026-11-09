<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Edit Units</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

    <form method="POST" action="{{ route('units.update', $unit) }}" class="space-y-4 max-w-xl">@csrf @method('PUT')
    <div><label class="block text-sm font-medium">Name</label><input type="text" name="name" value="{{ old('name', $unit->name) }}" class="sf-input">@error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
<div><label class="block text-sm font-medium">Short name (pcs, kg)</label><input type="text" name="short_name" value="{{ old('short_name', $unit->short_name) }}" class="sf-input">@error('short_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
    <button class="sf-btn-primary">Update</button> <a href="{{ route('units.index') }}" class="text-sm text-gray-600">Cancel</a></form>
    </div></div></div>
</x-app-layout>
