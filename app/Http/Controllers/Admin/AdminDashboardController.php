<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Listing;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_owners' => User::where('role', 'business_owner')->count(),
            'pending_owners' => User::where('role', 'business_owner')->where('approval_status', 'pending')->count(),
            'pending_businesses' => Business::where('status', 'pending')->count(),
            'pending_listings' => Listing::where('status', 'pending_review')->count(),
            'total_listings' => Listing::where('status', 'published')->count(),
            'total_bookings' => Booking::count(),
            'bookings_this_month' => Booking::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        $recentActivity = \App\Models\ActivityLog::with('user:id,name')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentActivity'));
    }
}
