<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Workshop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $admin = Auth::guard('admin')->user();
        $today = Carbon::today();

        // Base query scoped to this admin
        $baseQuery = Booking::where('admin_id', $admin->id);

        // Today's bookings
        $todayBookings = (clone $baseQuery)
            ->with('client')
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('start_time')
            ->get();

        // This week's bookings (Mon-Sun)
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $today->copy()->endOfWeek(Carbon::SUNDAY);
        $weekBookings = (clone $baseQuery)
            ->with('client')
            ->whereBetween('booking_date', [$weekStart, $weekEnd])
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();

        // This month's bookings
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $monthBookings = (clone $baseQuery)
            ->with('client')
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();

        // Bookings for a specific date (if requested via calendar click)
        $selectedDate = $request->get('date');
        $selectedDateBookings = null;
        if ($selectedDate) {
            $selectedDateBookings = (clone $baseQuery)
                ->with('client')
                ->whereDate('booking_date', $selectedDate)
                ->whereIn('status', ['confirmed', 'pending'])
                ->orderBy('start_time')
                ->get();
        }

        // Pending actions - bookings awaiting confirmation
        $pendingBookings = (clone $baseQuery)
            ->with('client')
            ->where('status', 'pending')
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Stats
        $stats = [
            'total_clients'       => Client::count(),
            'today_sessions'      => $todayBookings->count(),
            'week_sessions'       => $weekBookings->count(),
            'month_sessions'      => $monthBookings->count(),
            'pending_count'       => (clone $baseQuery)->where('status', 'pending')->count(),
            'confirmed_count'     => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'completed_this_month' => (clone $baseQuery)
                ->where('status', 'completed')
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->count(),
            'new_clients_month'   => Client::where('created_at', '>=', $monthStart)->count(),
        ];

        // Upcoming workshops
        $upcomingWorkshops = Workshop::active()
            ->upcoming()
            ->limit(3)
            ->get();

        // Calendar data - dates that have bookings this month (for calendar dots)
        $calendarDates = (clone $baseQuery)
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->whereIn('status', ['confirmed', 'pending'])
            ->selectRaw('booking_date, count(*) as count')
            ->groupBy('booking_date')
            ->pluck('count', 'booking_date')
            ->toArray();

        return Inertia::render('Admin/Dashboard', [
            'auth'                 => ['user' => $admin],
            'todayBookings'        => $todayBookings,
            'weekBookings'         => $weekBookings,
            'monthBookings'        => $monthBookings,
            'selectedDateBookings' => $selectedDateBookings,
            'selectedDate'         => $selectedDate,
            'pendingBookings'      => $pendingBookings,
            'stats'                => $stats,
            'upcomingWorkshops'    => $upcomingWorkshops,
            'calendarDates'        => $calendarDates,
        ]);
    }
}
