<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use App\Models\Client;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BroadcastController extends Controller
{
    public function index()
    {
        $recipients = $this->getUniqueRecipients();

        $history = Broadcast::with('admin')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'subject' => $b->subject,
                'total_recipients' => $b->total_recipients,
                'sent_count' => $b->sent_count,
                'failed_count' => $b->failed_count,
                'status' => $b->status,
                'sent_at' => $b->sent_at?->toDateTimeString(),
                'admin_name' => $b->admin?->name ?? 'Unknown',
                'created_at' => $b->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Broadcasting/Index', [
            'recipients' => $recipients,
            'history' => $history,
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'email',
        ]);

        $body = $validated['body'];

        $broadcast = Broadcast::create([
            'admin_id' => Auth::guard('admin')->id(),
            'subject' => $validated['subject'],
            'body' => $body,
            'recipients' => $validated['recipients'],
            'total_recipients' => count($validated['recipients']),
            'status' => 'sending',
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($validated['recipients'] as $email) {
            try {
                Mail::to($email)->send(new BroadcastMail($validated['subject'], $body));
                $sent++;
            } catch (\Throwable $e) {
                Log::error("Broadcast #{$broadcast->id} failed for {$email}: {$e->getMessage()}");
                $failed++;
            }
        }

        $broadcast->update([
            'sent_count' => $sent,
            'failed_count' => $failed,
            'status' => $failed === count($validated['recipients']) ? 'failed' : 'completed',
            'sent_at' => now(),
        ]);

        return back()->with('success', "Broadcast sent to {$sent} recipient(s)!" . ($failed ? " ({$failed} failed)" : ''));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $path = $request->file('image')->store('broadcasts', 'public');

        return response()->json([
            'url' => '/storage/' . $path,
        ]);
    }

    private function getUniqueRecipients(): array
    {
        $newsletterEmails = NewsletterSubscriber::where('is_active', true)
            ->pluck('email', 'name')
            ->mapWithKeys(fn ($email, $name) => [$email => [
                'email' => $email,
                'name' => $name ?: null,
                'source' => 'newsletter',
            ]]);

        $clientEmails = Client::pluck('email', 'name')
            ->mapWithKeys(fn ($email, $name) => [$email => [
                'email' => $email,
                'name' => $name ?: null,
                'source' => 'registered',
            ]]);

        // Merge — registered users take priority if duplicate
        $merged = $newsletterEmails->toArray();
        foreach ($clientEmails as $email => $data) {
            if (isset($merged[$email])) {
                $merged[$email]['source'] = 'both';
                $merged[$email]['name'] = $data['name'] ?? $merged[$email]['name'];
            } else {
                $merged[$email] = $data;
            }
        }

        return array_values($merged);
    }
}
