<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $email,
        public readonly string $otp,
        public readonly int $minutes,
    ) {
        //
    }

    public function build(): self
    {
        return $this->subject('Kode Verifikasi Login SIM KKN UAD')
            ->view('emails.otp')
            ->with([
                'otp' => $this->otp,
                'minutes' => $this->minutes,
            ]);
    }
}

