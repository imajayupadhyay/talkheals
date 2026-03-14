<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($existing) {
            if (! $existing->is_active) {
                $existing->update(['is_active' => true, 'subscribed_at' => now()]);
                return back()->with('newsletter_success', 'Welcome back! You\'ve been re-subscribed.');
            }
            return back()->with('newsletter_success', 'You\'re already subscribed — we\'ll keep the good stuff coming!');
        }

        NewsletterSubscriber::create([
            'email'         => $validated['email'],
            'is_active'     => true,
            'subscribed_at' => now(),
        ]);

        return back()->with('newsletter_success', 'Welcome! Your first newsletter is on its way. 🌿');
    }
}
