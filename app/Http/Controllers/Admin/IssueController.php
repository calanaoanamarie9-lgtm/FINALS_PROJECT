<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(): View
    {
        $issues = Issue::query()
            ->with(['booking.customer', 'customer'])
            ->latest()
            ->paginate(20);

        return view('admin.issues.index', compact('issues'));
    }

    public function resolve(Request $request, Issue $issue): RedirectResponse
    {
        $issue->update(['status' => 'resolved']);

        return back()->with('status', __('Issue marked as resolved.'));
    }
}
