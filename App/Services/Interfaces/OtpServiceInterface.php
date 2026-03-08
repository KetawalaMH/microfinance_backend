<?php

namespace App\Services\Interfaces;

interface OtpServiceInterface
{
    /**
     * Generate and store OTP for the given identifier.
     */
    public function generateOtp(string $identifier);

    /**
     * Verify the provided OTP.
     */
    public function verifyOtp(string $identifier, string $otp);
}
