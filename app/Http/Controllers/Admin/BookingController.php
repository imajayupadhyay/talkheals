<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationClient;
use App\Models\Booking;
use App\Services\GoogleCalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(Request $request): Response
    {
        $admin = Auth::guard('admin')->user();

        $query = Booking::with('client')
            ->where('admin_id', $admin->id)
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $bookings = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => Booking::where('admin_id', $admin->id)->count(),
            'pending'   => Booking::where('admin_id', $admin->id)->where('status', 'pending')->count(),
            'confirmed' => Booking::where('admin_id', $admin->id)->where('status', 'confirmed')->count(),
            'today'     => Booking::where('admin_id', $admin->id)->where('booking_date', now()->toDateString())->count(),
            'free'      => Booking::where('admin_id', $admin->id)->where('type', 'free')->count(),
            'paid'      => Booking::where('admin_id', $admin->id)->where('type', 'paid')->count(),
        ];

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'stats'    => $stats,
            'filters'  => $request->only('status', 'type'),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        abort_if($booking->admin_id !== $admin->id, 403);

        $validated = $request->validate([
            'status'              => 'required|in:pending,confirmed,cancelled,completed',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $booking->status = $validated['status'];

        if ($validated['status'] === 'cancelled') {
            $booking->cancelled_at          = now();
            $booking->cancellation_reason   = $validated['cancellation_reason'] ?? null;
        }

        $booking->save();

        // When admin confirms: create Google Meet link & send confirmation email to client
        if ($validated['status'] === 'confirmed') {
            $booking->load(['client', 'admin']);

            // Generate Google Meet link if admin has connected Google Calendar
            try {
                app(GoogleCalendarService::class)->createMeetEvent($booking);
                $booking->refresh();
            } catch (\Exception $e) {
                Log::warning('Google Meet link creation failed: ' . $e->getMessage());
            }

            Mail::to($booking->client->email)->send(new BookingConfirmationClient($booking));

            return back()->with('success', 'Booking confirmed & confirmation email sent to ' . $booking->client->email);
        }

        return back()->with('success', 'Booking status updated.');
    }
}
