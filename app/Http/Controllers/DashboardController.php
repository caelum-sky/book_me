<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function customer(Request $request): View
    {
        $user = $request->user();

        $upcomingBookings = $user->bookings()
            ->with(['listing', 'business'])
            ->upcoming()
            ->orderBy('check_in')
            ->limit(5)
            ->get();

        $pastBookings = $user->bookings()
            ->with(['listing', 'business'])
            ->whereIn('status', ['completed', 'cancelled', 'no_show'])
            ->latest('check_in')
            ->limit(5)
            ->get();

        return view('dashboard.customer', compact('upcomingBookings', 'pastBookings'));
    }
}
