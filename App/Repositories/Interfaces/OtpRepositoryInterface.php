<?php

namespace App\Repositories\Interfaces;

interface OtpRepositoryInterface
{
    /**
     * Create or update an OTP record.
     */
    public function createOrUpdateOtp(string $identifier, string $otp, int $minutes);

    /**
     * Return a valid OTP record for an identifier.
     */
    public function getValidOtp(string $identifier);

    /**
     * Verify OTP with identifier and value.
     */
    public function verifyOtp(string $identifier, string $otp);
}
