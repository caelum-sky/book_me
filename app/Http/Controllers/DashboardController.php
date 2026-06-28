<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function customer(Request $request): View
    {
        $user = $request->user();

        $totalBookings = Booking::where('user_id', $user->id)->count();

        $totalSpent = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_price');

        $upcomingBookings = $user->bookings()
            ->with(['listing', 'business'])
            ->upcoming()
            ->orderBy('check_in')
            ->limit(5)
            ->get();

        $upcomingCount = $upcomingBookings->count();

        $recentBookings = $user->bookings()
            ->with(['listing', 'business'])
            ->whereIn('status', ['completed', 'cancelled', 'no_show'])
            ->latest('check_in')
            ->limit(5)
            ->get();

        return view('dashboard.customer', compact(
            'totalBookings',
            'totalSpent',
            'upcomingBookings',
            'upcomingCount',
            'recentBookings',
        ));
    }
}