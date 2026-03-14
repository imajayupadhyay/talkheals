<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public string $reminderType;
    public string $recipientType;

    public function __construct(
        public Booking $booking,
        string $reminderType,
        string $recipientType = 'client',
    ) {
        $this->reminderType = $reminderType;
        $this->recipientType = $recipientType;
    }

    public function envelope(): Envelope
    {
        $labels = [
            '24h' => '24 Hours',
            '12h' => '12 Hours',
            '30min' => '30 Minutes',
        ];

        $timeLabel = $labels[$this->reminderType] ?? $this->reminderType;

        return new Envelope(
            subject: "Reminder: Session in {$timeLabel}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-reminder',
            with: [
                'booking' => $this->booking,
                'reminderType' => $this->reminderType,
                'recipientType' => $this->recipientType,
                'timeLabel' => match ($this->reminderType) {
                    '24h' => '24 hours',
                    '12h' => '12 hours',
                    '30min' => '30 minutes',
                    default => $this->reminderType,
                },
            ],
        );
    }
}
