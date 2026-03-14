<?php

namespace App\Console\Commands;

use App\Mail\BookingReminder;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Send reminder emails for upcoming bookings (24h, 12h, 30min)';

    public function handle(): int
    {
        $now = now();

        $bookings = Booking::with(['client', 'admin'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', $now->toDateString())
            ->where(function ($q) {
                $q->where('reminder_24h_sent', false)
                  ->orWhere('reminder_12h_sent', false)
                  ->orWhere('reminder_30min_sent', false);
            })
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            $sessionStart = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
            $minutesUntil = $now->diffInMinutes($sessionStart, false);

            // Skip past sessions
            if ($minutesUntil < 0) {
                continue;
            }

            // 24h reminder: send when ≤ 24 hours away
            if (! $booking->reminder_24h_sent && $minutesUntil <= 1440) {
                $this->sendReminder($booking, '24h');
                $booking->update(['reminder_24h_sent' => true]);
                $sent++;
            }

            // 12h reminder: send when ≤ 12 hours away
            if (! $booking->reminder_12h_sent && $minutesUntil <= 720) {
                $this->sendReminder($booking, '12h');
                $booking->update(['reminder_12h_sent' => true]);
                $sent++;
            }

            // 30min reminder: send when ≤ 30 minutes away
            if (! $booking->reminder_30min_sent && $minutesUntil <= 30) {
                $this->sendReminder($booking, '30min');
                $booking->update(['reminder_30min_sent' => true]);
                $sent++;
            }
        }

        $this->info("Sent {$sent} reminder(s).");

        return Command::SUCCESS;
    }

    private function sendReminder(Booking $booking, string $type): void
    {
        // Send to client
        Mail::to($booking->client->email)
            ->send(new BookingReminder($booking, $type, 'client'));

        // Send to admin
        Mail::to($booking->admin->email)
            ->send(new BookingReminder($booking, $type, 'admin'));
    }
}
