<?php

namespace App\Http\Controllers\Auth;

use App\Enums\StaffApplicationStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\StaffApplicationReceivedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class StaffRegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register-staff');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => UserRole::Staff,
            'is_active' => false,
            'staff_application_status' => StaffApplicationStatus::Pending,
        ]);

        event(new Registered($user));

        $user->notify(new StaffApplicationReceivedNotification);

        if ($user->phone) {
            app(\App\Services\SmsService::class)->send(
                $user->phone,
                'CleanSwift: staff application received. Await admin approval.'
            );
        }

        return redirect()->route('login')->with('status', __('Application submitted. Please wait for admin approval before signing in.'));
    }
}
