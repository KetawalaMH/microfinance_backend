<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     */
    public function build(): ResetPasswordOtpMail
    {
        return $this->subject(subject: 'Your Password Reset OTP')
            ->markdown(view: 'emails.reset_password_otp')
            ->with(key: [
                'otp' => $this->otp,
            ]);
    }
}
