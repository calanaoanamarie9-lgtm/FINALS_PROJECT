<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\StaffApplicationStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->role === UserRole::Admin) {
            $todayBookings = Booking::query()->whereDate('created_at', today())->count();
            $revenueToday = (float) Transaction::query()
                ->where('type', 'payment')
                ->whereDate('recorded_at', today())
                ->sum('amount');
            $pendingStaff = User::query()
                ->where('role', UserRole::Staff)
                ->where('staff_application_status', StaffApplicationStatus::Pending)
                ->count();

            return view('admin.dashboard', compact('todayBookings', 'revenueToday', 'pendingStaff'));
        }

        if ($user->role === UserRole::Staff) {
            $assignedOpen = Booking::query()
                ->where('assigned_staff_id', $user->id)
                ->whereNotIn('status', [BookingStatus::Delivered, BookingStatus::Cancelled])
                ->count();

            $upcoming = Booking::query()
                ->where('assigned_staff_id', $user->id)
                ->whereNotIn('status', [BookingStatus::Delivered, BookingStatus::Cancelled])
                ->orderBy('pickup_scheduled_at')
                ->limit(5)
                ->get();

            return view('staff.dashboard', compact('assignedOpen', 'upcoming'));
        }

        $upcomingBooking = Booking::query()
            ->where('customer_id', $user->id)
            ->whereNotIn('status', [BookingStatus::Delivered, BookingStatus::Cancelled])
            ->orderBy('pickup_scheduled_at')
            ->first();

        $services = Service::query()->where('is_active', true)->orderBy('sort_order')->limit(4)->get();

        return view('customer.dashboard', compact('upcomingBooking', 'services'));
    }
}
