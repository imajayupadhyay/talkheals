<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Email;

class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $mailBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function build(): self
    {
        $body = $this->mailBody;
        $images = [];

        // Find all images pointing to /storage/ (relative or absolute URLs)
        preg_match_all('/src=["\'](?:https?:\/\/[^\/]+)?\/storage\/([^"\']+)["\']/i', $body, $matches, PREG_SET_ORDER);

        foreach ($matches as $i => $match) {
            $storagePath = $match[1];
            $fullMatch = $match[0];

            if (!Storage::disk('public')->exists($storagePath)) {
                continue;
            }

            $cidName = 'broadcast_img_' . $i;
            $images[] = [
                'path' => Storage::disk('public')->path($storagePath),
                'cid' => $cidName,
                'mime' => Storage::disk('public')->mimeType($storagePath),
            ];

            $body = str_replace($fullMatch, 'src="cid:' . $cidName . '"', $body);
        }

        $this->view('emails.broadcast', ['body' => $body]);

        // Embed images as inline CID attachments via Symfony mailer
        if (!empty($images)) {
            $this->withSymfonyMessage(function (Email $message) use ($images) {
                foreach ($images as $img) {
                    $message->embedFromPath($img['path'], $img['cid'], $img['mime']);
                }
            });
        }

        return $this;
    }
}
