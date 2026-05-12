<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StaffApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffApplicationController extends Controller
{
    public function index(): View
    {
        $applications = User::query()
            ->where('role', \App\Enums\UserRole::Staff)
            ->where('staff_application_status', StaffApplicationStatus::Pending)
            ->latest()
            ->paginate(20);

        return view('admin.staff-applications.index', compact('applications'));
    }

    public function approve(User $user): RedirectResponse
    {
        abort_unless($user->role === \App\Enums\UserRole::Staff, 404);
        abort_unless($user->staff_application_status === StaffApplicationStatus::Pending, 404);

        $user->update([
            'staff_application_status' => StaffApplicationStatus::Approved,
            'is_active' => true,
        ]);

        return back()->with('status', __('Staff application approved.'));
    }

    public function reject(User $user): RedirectResponse
    {
        abort_unless($user->role === \App\Enums\UserRole::Staff, 404);
        abort_unless($user->staff_application_status === StaffApplicationStatus::Pending, 404);

        $user->update([
            'staff_application_status' => StaffApplicationStatus::Rejected,
            'is_active' => false,
        ]);

        return back()->with('status', __('Staff application rejected.'));
    }
}
