<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::latest('subscribed_at');

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subscribers = $query->get();

        return Inertia::render('Admin/Newsletter/Index', [
            'subscribers' => $subscribers,
            'filters'     => $request->only('search', 'status'),
            'stats'       => [
                'total'       => NewsletterSubscriber::count(),
                'active'      => NewsletterSubscriber::active()->count(),
                'unsubscribed'=> NewsletterSubscriber::where('is_active', false)->count(),
                'this_month'  => NewsletterSubscriber::whereMonth('subscribed_at', now()->month)
                                    ->whereYear('subscribed_at', now()->year)->count(),
            ],
        ]);
    }

    public function toggleActive(NewsletterSubscriber $subscriber)
    {
        $subscriber->update(['is_active' => ! $subscriber->is_active]);

        return back()->with('success', $subscriber->is_active ? 'Subscriber reactivated.' : 'Subscriber unsubscribed.');
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber deleted.');
    }
}
