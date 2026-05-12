<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $period = $request->string('period')->toString() ?: 'daily';

        $start = match ($period) {
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->startOfDay(),
        };

        $bookingsInRange = Booking::query()->where('created_at', '>=', $start)->count();

        $revenue = (float) Transaction::query()
            ->where('type', 'payment')
            ->where('recorded_at', '>=', $start)
            ->sum('amount');

        return view('admin.reports', compact('period', 'revenue', 'start', 'bookingsInRange'));
    }
}
