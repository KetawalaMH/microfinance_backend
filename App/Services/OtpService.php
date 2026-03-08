<?php

namespace App\Services;

use App\Repositories\Interfaces\OtpRepositoryInterface;
use App\Repositories\OtpRepository;
use App\Services\Interfaces\OtpServiceInterface;
use Illuminate\Support\Str;

class OtpService implements OtpServiceInterface
{
    protected $otpRepository;
    const OTP_LENGTH = 6;
    const OTP_EXPIRY = 5; // minutes

    public function __construct(OtpRepositoryInterface $otpRepository)
    {
        $this->otpRepository = $otpRepository;
    }

    public function generateOtp(string $identifier)
    {
        // Secure random OTP
        $otp = str_pad(random_int(100000, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);

        // Save to DB
        return $this->otpRepository->createOrUpdateOtp($identifier, $otp, self::OTP_EXPIRY);
    }

    public function verifyOtp(string $identifier, string $otp)
    {
        $result = $this->otpRepository->verifyOtp($identifier, $otp);

        if (!$result) {
            return false;
        }

        return true;
    }
}
