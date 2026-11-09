<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Accounts') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 rounded bg-green-100 px-4 py-2 text-green-800">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 rounded bg-red-100 px-4 py-2 text-red-800">{{ $errors->first() }}</div>
                    @endif

                    <table class="sf-table">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="py-2 pr-4">Name</th>
                                <th class="py-2 pr-4">Email</th>
                                <th class="py-2 pr-4">Phone</th>
                                <th class="py-2 pr-4">Role</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="border-b align-top">
                                    <td class="py-2 pr-4">{{ $user->name }}</td>
                                    <td class="py-2 pr-4">{{ $user->email }}</td>
                                    <td class="py-2 pr-4">{{ $user->phone ?? '—' }}</td>
                                    <td class="py-2 pr-4">
                                        <form method="POST" action="{{ route('staff.role', $user) }}" class="flex gap-1">
                                            @csrf @method('PATCH')
                                            <select name="role" class="sf-select">
                                                @foreach (\App\Enums\UserRole::cases() as $role)
                                                    <option value="{{ $role->value }}" @selected($user->role === $role)>{{ ucfirst($role->value) }}</option>
                                                @endforeach
                                            </select>
                                            <button class="rounded bg-gray-700 px-2 py-1 text-white">Save</button>
                                        </form>
                                    </td>
                                    <td class="py-2 pr-4">
                                        <span class="rounded px-2 py-1 {{ $user->status->value === 'approved' ? 'bg-green-100 text-green-800' : ($user->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($user->status->value) }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        <div class="flex flex-wrap gap-1">
                                            @if ($user->status->value === 'pending' || $user->status->value === 'rejected')
                                                <form method="POST" action="{{ route('staff.approve', $user) }}">@csrf
                                                    <button class="rounded bg-green-600 px-2 py-1 text-white">Approve</button>
                                                </form>
                                            @endif
                                            @if ($user->status->value === 'pending')
                                                <form method="POST" action="{{ route('staff.reject', $user) }}">@csrf
                                                    <button class="rounded bg-red-600 px-2 py-1 text-white">Reject</button>
                                                </form>
                                            @endif
                                            @if ($user->status->value === 'approved' || $user->status->value === 'inactive')
                                                <form method="POST" action="{{ route('staff.toggle', $user) }}">@csrf
                                                    <button class="rounded bg-gray-600 px-2 py-1 text-white">{{ $user->status->value === 'inactive' ? 'Activate' : 'Deactivate' }}</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-4 text-center text-gray-500">No staff accounts yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
