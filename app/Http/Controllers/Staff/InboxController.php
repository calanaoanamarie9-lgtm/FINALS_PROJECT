<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $notifications = $user->notifications()->paginate(20);

        return view('staff.notifications.index', compact('notifications'));
    }

    public function read(Request $request, string $id): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return back();
    }

    public function unreadCount(Request $request): \Illuminate\Http\JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
        ]);
    }
}
