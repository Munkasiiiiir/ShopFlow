<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Admin staff management (FR-1.5, FR-1.6). Admin only (role middleware). */
class StaffController extends Controller
{
    public function index(): View
    {
        $users = User::orderByRaw("CASE status WHEN 'pending' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(20);

        return view('staff.index', compact('users'));
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'status' => AccountStatus::Approved,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('status', "{$user->name} has been approved.");
    }

    public function reject(User $user): RedirectResponse
    {
        $user->update(['status' => AccountStatus::Rejected, 'approved_by' => null, 'approved_at' => null]);

        return back()->with('status', "{$user->name} has been rejected.");
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        if ($user->is($request->user()) && $data['role'] !== UserRole::Admin->value) {
            return back()->withErrors(['role' => 'You cannot remove your own Admin role.']);
        }

        $user->update(['role' => $data['role']]);

        return back()->with('status', "{$user->name}'s role updated to {$data['role']}.");
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['status' => 'You cannot deactivate your own account.']);
        }

        $user->update([
            'status' => $user->status === AccountStatus::Inactive
                ? AccountStatus::Approved
                : AccountStatus::Inactive,
        ]);

        return back()->with('status', "{$user->name}'s account status updated.");
    }
}
