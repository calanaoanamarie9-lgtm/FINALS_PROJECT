<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StaffApplicationStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->string('role')->toString();

        $users = User::query()
            ->when($role, fn ($q) => $q->where('role', $role))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'role'));
    }

    public function createStaff(): View
    {
        return view('admin.users.create-staff');
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => UserRole::Staff,
            'is_active' => true,
            'staff_application_status' => StaffApplicationStatus::Approved,
        ]);

        return redirect()->route('admin.users.index', ['role' => 'staff'])->with('status', __('Staff account created.'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role === UserRole::Admin && ! $request->boolean('is_active')) {
            return back()->withErrors(['is_active' => __('Administrator accounts must remain active.')]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        $user->update([
            ...$data,
            'email' => Str::lower($data['email']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', __('User updated.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role === UserRole::Admin) {
            return back()->withErrors(['user' => __('Admin accounts cannot be deleted here.')]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', __('User deleted.'));
    }
}
