<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class ServiceBrowseController extends Controller
{
    public function index(): View
    {
        $services = Service::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('customer.services', compact('services'));
    }
}
