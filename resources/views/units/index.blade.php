<x-app-layout>
    <x-slot name="header"><div><h2 class="sf-page-title">Units</h2></div></x-slot>
    <div class="p-4 md:p-6">
    <div class="sf-card"><div class="p-5 md:p-6 text-slate-800">
    <x-flash />

    <div class="mb-4"><a href="{{ route('units.create') }}" class="sf-btn-primary">+ New</a></div>
    <table class="sf-table"><thead><tr class="border-b text-left"><th class='py-2 pr-4'>Name</th><th class='py-2 pr-4'>Short</th><th class="py-2">Actions</th></tr></thead>
    <tbody>@forelse ($units as $unit)
        <tr class="border-b"><td class='py-2 pr-4'>{{ $unit->name }}</td><td class='py-2 pr-4'>{{ $unit->short_name }}</td>
        <td class="py-2 whitespace-nowrap"><a class="font-medium text-brand-700 hover:text-brand-600" href="{{ route('units.edit', $unit) }}">Edit</a>
        <form method="POST" action="{{ route('units.destroy', $unit) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
        <button class="text-red-600 ml-2">Delete</button></form></td></tr>
    @empty<tr><td colspan="9" class="py-4 text-center text-gray-500">No records.</td></tr>@endforelse</tbody></table>
    <div class="mt-4">{{ $units->links() }}</div>
    </div></div></div>
</x-app-layout>
