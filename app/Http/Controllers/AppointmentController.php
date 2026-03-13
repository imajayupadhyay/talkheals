<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        $client = Auth::user();

        $bookings = Booking::where('client_id', $client->id)
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->get([
                'id', 'booking_date', 'start_time', 'end_time',
                'status', 'type', 'session_type',
                'client_notes', 'cancellation_reason', 'cancelled_at',
                'created_at',
            ]);

        $stats = [
            'total'     => $bookings->count(),
            'upcoming'  => $bookings->whereIn('status', ['pending', 'confirmed'])
                                    ->where('booking_date', '>=', now()->toDateString())
                                    ->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
        ];

        return Inertia::render('Dashboard/Appointments', [
            'bookings' => $bookings,
            'stats'    => $stats,
        ]);
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $client = Auth::user();
        abort_if($booking->client_id !== $client->id, 403);
        abort_if(! in_array($booking->status, ['pending', 'confirmed']), 422);

        $booking->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Booking cancelled.');
    }
}
