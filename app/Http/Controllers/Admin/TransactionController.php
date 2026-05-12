<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::query()
            ->with('booking.customer')
            ->latest('recorded_at')
            ->paginate(25);

        return view('admin.transactions.index', compact('transactions'));
    }
}
