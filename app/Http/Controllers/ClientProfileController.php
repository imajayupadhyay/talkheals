<?php

namespace App\Http\Controllers;

use App\Models\ClientProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClientProfileController extends Controller
{
    /**
     * Display the client's clinical profile form.
     */
    public function edit(Request $request): Response
    {
        $client = $request->user();
        
        // Ensure a profile exists
        $profile = $client->profile ?? ClientProfile::create(['client_id' => $client->id]);

        return Inertia::render('Dashboard/Profile', [
            'profile' => $profile,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the client's clinical profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'preferred_name' => 'nullable|string|max:255',
            'pronouns' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender_identity' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'timezone' => 'required|string',
            'occupation' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'reason_for_therapy' => 'nullable|string',
            'previous_therapy_experience' => 'boolean',
            'current_medications' => 'nullable|string',
            'mental_health_history' => 'nullable|string',
        ]);

        $client = Auth::user();
        $client->profile()->update($request->all());

        return redirect()->route('client.profile.edit')->with('status', 'clinical-profile-updated');
    }
}
