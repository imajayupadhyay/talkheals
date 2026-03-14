<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirect(GoogleCalendarService $google)
    {
        return redirect()->away($google->getAuthUrl());
    }

    public function callback(Request $request, GoogleCalendarService $google)
    {
        if ($request->has('error')) {
            return redirect()->route('admin.availability')
                ->with('error', 'Google authorization was denied.');
        }

        $google->handleCallback($request->get('code'), Auth::guard('admin')->user());

        return redirect()->route('admin.availability')
            ->with('success', 'Google Calendar connected! Meet links will be auto-generated for new bookings.');
    }

    public function disconnect()
    {
        Auth::guard('admin')->user()->update(['google_token' => null]);

        return redirect()->route('admin.availability')
            ->with('success', 'Google Calendar disconnected.');
    }
}
