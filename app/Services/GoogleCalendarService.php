<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\ConferenceSolutionKey;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

class GoogleCalendarService
{
    private GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback(string $code, User $admin): void
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        $admin->update(['google_token' => $token]);
    }

    public function isConnected(User $admin): bool
    {
        return ! empty($admin->google_token);
    }

    public function createMeetEvent(Booking $booking): ?string
    {
        $admin = $booking->admin;

        if (! $this->isConnected($admin)) {
            return null;
        }

        $this->client->setAccessToken($admin->google_token);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $admin->google_token['refresh_token'] ?? null;

            if (! $refreshToken) {
                $admin->update(['google_token' => null]);
                return null;
            }

            $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (isset($newToken['error'])) {
                $admin->update(['google_token' => null]);
                return null;
            }

            // Preserve refresh token
            if (empty($newToken['refresh_token'])) {
                $newToken['refresh_token'] = $refreshToken;
            }

            $admin->update(['google_token' => $newToken]);
        }

        $calendar = new Calendar($this->client);

        $startDateTime = Carbon::parse(
            $booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time
        );
        $endDateTime = Carbon::parse(
            $booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time
        );

        $event = new Event([
            'summary' => $booking->session_type . ' — ' . $booking->client->name,
            'description' => $booking->client_notes
                ? "Client notes: {$booking->client_notes}"
                : 'Free Discovery Call',
            'start' => new EventDateTime([
                'dateTime' => $startDateTime->toRfc3339String(),
                'timeZone' => config('app.timezone', 'Asia/Kolkata'),
            ]),
            'end' => new EventDateTime([
                'dateTime' => $endDateTime->toRfc3339String(),
                'timeZone' => config('app.timezone', 'Asia/Kolkata'),
            ]),
            'attendees' => [
                ['email' => $booking->client->email],
                ['email' => $admin->email],
            ],
            'conferenceData' => new ConferenceData([
                'createRequest' => new CreateConferenceRequest([
                    'conferenceSolutionKey' => new ConferenceSolutionKey([
                        'type' => 'hangoutsMeet',
                    ]),
                    'requestId' => 'talkheals-' . $booking->id . '-' . time(),
                ]),
            ]),
        ]);

        $createdEvent = $calendar->events->insert('primary', $event, [
            'conferenceDataVersion' => 1,
            'sendUpdates' => 'all',
        ]);

        $meetLink = $createdEvent->getHangoutLink();

        $booking->update([
            'google_meet_link' => $meetLink,
            'google_event_id' => $createdEvent->getId(),
        ]);

        return $meetLink;
    }
}
