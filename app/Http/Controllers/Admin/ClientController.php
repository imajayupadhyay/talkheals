<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Client::with('profile')
            ->withCount('bookings')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term);
            });
        }

        $clients = $query->paginate(12)->withQueryString();

        $stats = [
            'total'          => Client::count(),
            'new_this_month' => Client::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)->count(),
            'with_bookings'  => Client::has('bookings')->count(),
            'active'         => Client::whereHas('bookings', fn ($q) =>
                                    $q->whereIn('status', ['pending', 'confirmed'])
                                      ->where('booking_date', '>=', now()->toDateString())
                                )->count(),
        ];

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
            'stats'   => $stats,
            'filters' => $request->only('search'),
        ]);
    }

    public function show(Client $client): JsonResponse
    {
        $client->load('profile');

        $bookings = $client->bookings()
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->get([
                'id', 'booking_date', 'start_time', 'end_time',
                'status', 'type', 'session_type', 'client_notes',
                'admin_notes', 'cancellation_reason', 'created_at',
            ]);

        return response()->json([
            'client'   => $client,
            'bookings' => $bookings,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:clients,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Client::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Client created successfully.');
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name'                           => 'required|string|max:255',
            'email'                          => 'required|email|max:255|unique:clients,email,' . $client->id,
            'password'                       => 'nullable|string|min:8|confirmed',
            // Profile
            'preferred_name'                 => 'nullable|string|max:255',
            'pronouns'                       => 'nullable|string|max:100',
            'date_of_birth'                  => 'nullable|date',
            'gender_identity'                => 'nullable|string|max:100',
            'phone'                          => 'nullable|string|max:50',
            'timezone'                       => 'nullable|string|max:100',
            'occupation'                     => 'nullable|string|max:255',
            'emergency_contact_name'         => 'nullable|string|max:255',
            'emergency_contact_phone'        => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'reason_for_therapy'             => 'nullable|string',
            'previous_therapy_experience'    => 'nullable|boolean',
            'current_medications'            => 'nullable|string',
            'mental_health_history'          => 'nullable|string',
        ]);

        $client->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            ...($validated['password'] ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        $client->profile()->updateOrCreate(
            ['client_id' => $client->id],
            collect($validated)->except(['name', 'email', 'password', 'password_confirmation'])->toArray()
        );

        return back()->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return back()->with('success', 'Client deleted.');
    }
}
