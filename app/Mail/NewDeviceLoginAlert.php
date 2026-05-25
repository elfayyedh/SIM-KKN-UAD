<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewDeviceLoginAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $history;
    public $user;

    public function __construct($history, $user)
    {
        $this->history = $history;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peringatan: Aktivitas Login Baru - SIM KKN UAD',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_device_alert',
        );
    }
}
