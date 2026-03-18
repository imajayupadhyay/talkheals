<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Workshop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:100',
        ]);

        $query = trim($request->q);
        $admin = Auth::guard('admin')->user();
        $results = [];

        // Search Clients
        $clients = Client::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'email', 'created_at']);

        foreach ($clients as $client) {
            $results[] = [
                'type'        => 'client',
                'id'          => $client->id,
                'title'       => $client->name,
                'subtitle'    => $client->email,
                'url'         => route('admin.clients'),
                'meta'        => 'Joined ' . $client->created_at->format('M j, Y'),
            ];
        }

        // Search Bookings (by client name, session type, or date)
        $bookings = Booking::with('client')
            ->where('admin_id', $admin->id)
            ->where(function ($q) use ($query) {
                $q->where('session_type', 'like', "%{$query}%")
                  ->orWhere('booking_date', 'like', "%{$query}%")
                  ->orWhere('status', 'like', "%{$query}%")
                  ->orWhereHas('client', function ($cq) use ($query) {
                      $cq->where('name', 'like', "%{$query}%")
                         ->orWhere('email', 'like', "%{$query}%");
                  });
            })
            ->orderByDesc('booking_date')
            ->limit(5)
            ->get();

        foreach ($bookings as $booking) {
            $results[] = [
                'type'        => 'booking',
                'id'          => $booking->id,
                'title'       => ($booking->client->name ?? 'Unknown') . ' — ' . ($booking->session_type ?? 'Session'),
                'subtitle'    => $booking->booking_date->format('M j, Y') . ' at ' . $this->formatTime($booking->start_time),
                'url'         => route('admin.bookings'),
                'meta'        => ucfirst($booking->status) . ' · ' . ucfirst($booking->type),
                'status'      => $booking->status,
            ];
        }

        // Search Workshops
        $workshops = Workshop::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderByDesc('workshop_date')
            ->limit(5)
            ->get();

        foreach ($workshops as $ws) {
            $results[] = [
                'type'        => 'workshop',
                'id'          => $ws->id,
                'title'       => $ws->title,
                'subtitle'    => $ws->workshop_date->format('M j, Y') . ($ws->workshop_time ? ' at ' . $this->formatTime($ws->workshop_time) : ''),
                'url'         => route('admin.workshops'),
                'meta'        => ($ws->is_free ? 'Free' : 'Paid') . ' · ' . $ws->duration_minutes . 'min',
            ];
        }

        return response()->json([
            'results' => $results,
            'query'   => $query,
            'total'   => count($results),
        ]);
    }

    private function formatTime(?string $time): string
    {
        if (!$time) return '';
        $parts = explode(':', $time);
        $hour = (int) $parts[0];
        $min = $parts[1] ?? '00';
        $ampm = $hour >= 12 ? 'PM' : 'AM';
        $h12 = $hour % 12 ?: 12;
        return "{$h12}:{$min} {$ampm}";
    }
}
