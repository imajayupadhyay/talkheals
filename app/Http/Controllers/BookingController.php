<?php

namespace App\Http\Controllers;

use App\Models\AdminAvailability;
use App\Models\AdminAvailabilityException;
use App\Models\Booking;
use App\Models\BookingSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Return which dates in the booking window have at least one available slot.
     */
    public function getCalendar(Request $request): JsonResponse
    {
        $admin = User::first();
        if (! $admin) {
            return response()->json(['available_dates' => [], 'advance_booking_days' => 30]);
        }

        $settings = BookingSetting::where('admin_id', $admin->id)->first();

        if (! $settings || ! $settings->is_booking_enabled) {
            return response()->json(['available_dates' => [], 'advance_booking_days' => 30]);
        }

        $today    = now()->toDateString();
        $maxDate  = now()->addDays($settings->advance_booking_days)->toDateString();

        // Days of week that have active availability
        $activeDays = AdminAvailability::where('admin_id', $admin->id)
            ->where('is_active', true)
            ->pluck('day_of_week')
            ->unique()
            ->toArray();

        // Exceptions in range, keyed by date string
        $exceptions = AdminAvailabilityException::where('admin_id', $admin->id)
            ->whereBetween('date', [$today, $maxDate])
            ->get()
            ->keyBy(fn ($e) => Carbon::parse($e->date)->toDateString());

        $availableDates = [];
        $current = now()->startOfDay();
        $end     = now()->addDays($settings->advance_booking_days)->startOfDay();

        while ($current->lte($end)) {
            $dateStr   = $current->toDateString();
            $dayOfWeek = $current->dayOfWeek; // 0=Sun … 6=Sat

            $exception = $exceptions[$dateStr] ?? null;

            if ($exception) {
                // Blocked → skip; custom hours → available
                if (! $exception->is_blocked) {
                    $availableDates[] = $dateStr;
                }
            } elseif (in_array($dayOfWeek, $activeDays)) {
                $availableDates[] = $dateStr;
            }

            $current->addDay();
        }

        return response()->json([
            'available_dates'      => $availableDates,
            'advance_booking_days' => $settings->advance_booking_days,
        ]);
    }

    /**
     * Return available 30-min time slots for a given date (free discovery calls).
     */
    public function getSlots(Request $request): JsonResponse
    {
        $date = $request->validate(['date' => 'required|date|after_or_equal:today'])['date'];

        $admin = User::first();
        if (! $admin) {
            return response()->json([]);
        }

        $settings = BookingSetting::where('admin_id', $admin->id)->first();

        if (! $settings || ! $settings->is_booking_enabled) {
            return response()->json([]);
        }

        $dayOfWeek  = Carbon::parse($date)->dayOfWeek;
        $exception  = AdminAvailabilityException::where('admin_id', $admin->id)
            ->where('date', $date)
            ->first();

        // Determine availability windows for this date
        if ($exception && $exception->is_blocked) {
            return response()->json([]);
        }

        if ($exception && ! $exception->is_blocked) {
            $windows = [['start' => substr($exception->start_time, 0, 5), 'end' => substr($exception->end_time, 0, 5)]];
        } else {
            $windows = AdminAvailability::where('admin_id', $admin->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->get(['start_time', 'end_time'])
                ->map(fn ($a) => ['start' => substr($a->start_time, 0, 5), 'end' => substr($a->end_time, 0, 5)])
                ->toArray();
        }

        if (empty($windows)) {
            return response()->json([]);
        }

        // Free discovery call is always 30 minutes
        $slotDuration = 30;
        $buffer       = $settings->buffer_time;

        // Existing bookings on this date (pending or confirmed)
        $booked = Booking::where('admin_id', $admin->id)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['start_time', 'end_time']);

        $slots = [];

        foreach ($windows as $window) {
            $current   = Carbon::parse($date . ' ' . $window['start']);
            $windowEnd = Carbon::parse($date . ' ' . $window['end']);

            while ($current->copy()->addMinutes($slotDuration)->lte($windowEnd)) {
                $slotEnd     = $current->copy()->addMinutes($slotDuration);
                $conflicting = false;

                foreach ($booked as $b) {
                    $bookedStart = Carbon::parse($date . ' ' . $b->start_time);
                    $bookedEnd   = Carbon::parse($date . ' ' . $b->end_time)->addMinutes($buffer);

                    if ($current->lt($bookedEnd) && $slotEnd->gt($bookedStart)) {
                        $conflicting = true;
                        break;
                    }
                }

                if (! $conflicting) {
                    $slots[] = [
                        'start' => $current->format('H:i'),
                        'end'   => $slotEnd->format('H:i'),
                        'label' => $current->format('g:i A'),
                    ];
                }

                $current->addMinutes($slotDuration);
            }
        }

        return response()->json($slots);
    }

    /**
     * Store a new free discovery call booking.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'         => 'required|date|after_or_equal:today',
            'start_time'   => 'required|date_format:H:i',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        $admin = User::first();
        if (! $admin) {
            return response()->json(['message' => 'Booking unavailable.'], 503);
        }

        $client = Auth::user();

        // Re-validate the slot is still available (race condition guard)
        $endTime = Carbon::parse($validated['date'] . ' ' . $validated['start_time'])
            ->addMinutes(30)
            ->format('H:i');

        $conflict = Booking::where('admin_id', $admin->id)
            ->where('booking_date', $validated['date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($validated, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'This slot was just taken. Please pick another.'], 409);
        }

        $booking = Booking::create([
            'client_id'    => $client->id,
            'admin_id'     => $admin->id,
            'booking_date' => $validated['date'],
            'start_time'   => $validated['start_time'],
            'end_time'     => $endTime,
            'status'       => 'pending',
            'type'         => 'free',
            'session_type' => 'Free Discovery Call',
            'client_notes' => $validated['client_notes'] ?? null,
        ]);

        return response()->json([
            'message'      => 'Booking confirmed!',
            'booking_date' => Carbon::parse($validated['date'])->format('l, F j, Y'),
            'start_time'   => Carbon::parse($validated['date'] . ' ' . $validated['start_time'])->format('g:i A'),
            'end_time'     => Carbon::parse($validated['date'] . ' ' . $endTime)->format('g:i A'),
        ], 201);
    }
}
