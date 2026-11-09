<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Customers</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

    <form method="GET" class="mb-4 flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search" class="sf-select">
    <button class="sf-btn-secondary">Search</button>
    <a href="{{ route('customers.create') }}" class="sf-btn-primary">+ New</a></form>
    <table class="sf-table"><thead><tr class="border-b text-left"><th class='py-2 pr-4'>Name</th><th class='py-2 pr-4'>Phone</th><th class="py-2 pr-4">Active</th><th class="py-2">Actions</th></tr></thead>
    <tbody>@forelse ($customers as $customer)
        <tr class="border-b"><td class='py-2 pr-4'>{{ $customer->name }}</td><td class='py-2 pr-4'>{{ $customer->phone ?? '—' }}</td>
        <td class="py-2 pr-4">{{ $customer->is_active ? 'Yes' : 'No' }}</td><td class="py-2 whitespace-nowrap"><a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('customers.show', $customer) }}">View</a>
        <a class="ml-2 font-medium text-brand-700 hover:text-brand-600" href="{{ route('customers.edit', $customer) }}">Edit</a>
        <form method="POST" action="{{ route('customers.toggle', $customer) }}" class="inline">@csrf
        <button class="text-gray-600 ml-2">{{ $customer->is_active ? 'Deactivate' : 'Activate' }}</button></form>
        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
        <button class="text-red-600 ml-2">Delete</button></form></td></tr>
    @empty<tr><td colspan="9" class="py-4 text-center text-gray-500">No records.</td></tr>@endforelse</tbody></table>
    <div class="mt-4">{{ $customers->links() }}</div>
    </div></div></div>
</x-app-layout>
