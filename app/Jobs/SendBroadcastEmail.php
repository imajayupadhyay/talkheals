<?php

namespace App\Jobs;

use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBroadcastEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Broadcast $broadcast,
    ) {}

    public function handle(): void
    {
        $this->broadcast->update(['status' => 'sending']);

        $sent = 0;
        $failed = 0;

        foreach ($this->broadcast->recipients as $email) {
            try {
                Mail::to($email)->send(
                    new BroadcastMail($this->broadcast->subject, $this->broadcast->body)
                );
                $sent++;
            } catch (\Throwable $e) {
                Log::error("Broadcast #{$this->broadcast->id} failed for {$email}: {$e->getMessage()}");
                $failed++;
            }

            $this->broadcast->update([
                'sent_count' => $sent,
                'failed_count' => $failed,
            ]);
        }

        $this->broadcast->update([
            'status' => $failed === count($this->broadcast->recipients) ? 'failed' : 'completed',
            'sent_at' => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->broadcast->update(['status' => 'failed']);
    }
}
