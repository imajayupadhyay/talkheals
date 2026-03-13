<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAvailability;
use App\Models\AdminAvailabilityException;
use App\Models\BookingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AvailabilityController extends Controller
{
    public function index(): Response
    {
        $admin = Auth::guard('admin')->user();

        $availability = AdminAvailability::where('admin_id', $admin->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get(['id', 'day_of_week', 'start_time', 'end_time', 'is_active']);

        $settings = BookingSetting::firstOrCreate(
            ['admin_id' => $admin->id],
            [
                'session_duration'    => 50,
                'buffer_time'         => 10,
                'advance_booking_days' => 30,
                'timezone'            => 'America/Toronto',
                'is_booking_enabled'  => true,
            ]
        );

        $exceptions = AdminAvailabilityException::where('admin_id', $admin->id)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get(['id', 'date', 'is_blocked', 'start_time', 'end_time', 'reason']);

        return Inertia::render('Admin/Availability/Index', [
            'availability' => $availability,
            'settings'     => $settings,
            'exceptions'   => $exceptions,
        ]);
    }

    public function saveSchedule(Request $request): RedirectResponse
    {
        $request->validate([
            'schedule'                  => ['required', 'array'],
            'schedule.*.active'         => ['required', 'boolean'],
            'schedule.*.slots'          => ['required', 'array'],
            'schedule.*.slots.*.start'  => ['required', 'date_format:H:i'],
            'schedule.*.slots.*.end'    => ['required', 'date_format:H:i'],
        ]);

        $admin = Auth::guard('admin')->user();

        AdminAvailability::where('admin_id', $admin->id)->delete();

        foreach ($request->schedule as $dayOfWeek => $dayData) {
            if (! $dayData['active'] || empty($dayData['slots'])) {
                continue;
            }

            foreach ($dayData['slots'] as $slot) {
                AdminAvailability::create([
                    'admin_id'    => $admin->id,
                    'day_of_week' => (int) $dayOfWeek,
                    'start_time'  => $slot['start'],
                    'end_time'    => $slot['end'],
                    'is_active'   => true,
                ]);
            }
        }

        return back()->with('success', 'Weekly schedule saved successfully.');
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_duration'    => ['required', 'integer', 'in:30,45,50,60,90'],
            'buffer_time'         => ['required', 'integer', 'in:0,5,10,15,30'],
            'advance_booking_days' => ['required', 'integer', 'in:7,14,30,60,90'],
            'timezone'            => ['required', 'string', 'max:100'],
            'is_booking_enabled'  => ['required', 'boolean'],
        ]);

        $admin = Auth::guard('admin')->user();

        BookingSetting::updateOrCreate(
            ['admin_id' => $admin->id],
            $validated
        );

        return back()->with('success', 'Settings updated successfully.');
    }

    public function addException(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date'       => ['required', 'date', 'after_or_equal:today'],
            'is_blocked' => ['required', 'boolean'],
            'start_time' => ['nullable', 'required_if:is_blocked,false', 'date_format:H:i'],
            'end_time'   => ['nullable', 'required_if:is_blocked,false', 'date_format:H:i', 'after:start_time'],
            'reason'     => ['nullable', 'string', 'max:255'],
        ]);

        $admin = Auth::guard('admin')->user();

        AdminAvailabilityException::updateOrCreate(
            ['admin_id' => $admin->id, 'date' => $validated['date']],
            array_merge($validated, ['admin_id' => $admin->id])
        );

        return back()->with('success', 'Date override added.');
    }

    public function removeException(AdminAvailabilityException $exception): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        abort_if($exception->admin_id !== $admin->id, 403);

        $exception->delete();

        return back()->with('success', 'Override removed.');
    }
}
