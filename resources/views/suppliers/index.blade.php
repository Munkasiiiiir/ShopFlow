<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Suppliers</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

    <form method="GET" class="mb-4 flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search" class="sf-select">
    <button class="sf-btn-secondary">Search</button>
    <a href="{{ route('suppliers.create') }}" class="sf-btn-primary">+ New</a></form>
    <table class="sf-table"><thead><tr class="border-b text-left"><th class='py-2 pr-4'>Name</th><th class='py-2 pr-4'>Company</th><th class='py-2 pr-4'>Phone</th><th class="py-2 pr-4">Active</th><th class="py-2">Actions</th></tr></thead>
    <tbody>@forelse ($suppliers as $supplier)
        <tr class="border-b"><td class='py-2 pr-4'>{{ $supplier->name }}</td><td class='py-2 pr-4'>{{ $supplier->company ?? '—' }}</td><td class='py-2 pr-4'>{{ $supplier->phone }}</td>
        <td class="py-2 pr-4">{{ $supplier->is_active ? 'Yes' : 'No' }}</td><td class="py-2 whitespace-nowrap"><a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('suppliers.show', $supplier) }}">View</a>
        <a class="ml-2 font-medium text-brand-700 hover:text-brand-600" href="{{ route('suppliers.edit', $supplier) }}">Edit</a>
        <form method="POST" action="{{ route('suppliers.toggle', $supplier) }}" class="inline">@csrf
        <button class="text-gray-600 ml-2">{{ $supplier->is_active ? 'Deactivate' : 'Activate' }}</button></form>
        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
        <button class="text-red-600 ml-2">Delete</button></form></td></tr>
    @empty<tr><td colspan="9" class="py-4 text-center text-gray-500">No records.</td></tr>@endforelse</tbody></table>
    <div class="mt-4">{{ $suppliers->links() }}</div>
    </div></div></div>
</x-app-layout>
