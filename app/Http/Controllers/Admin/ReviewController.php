<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()
            ->with(['booking.customer'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }
}
