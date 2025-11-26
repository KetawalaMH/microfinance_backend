<?php

namespace App\Repositories;

use App\Models\Otp;
use App\Repositories\Interfaces\OtpRepositoryInterface;
use Carbon\Carbon;

class OtpRepository implements OtpRepositoryInterface
{
    public function createOrUpdateOtp(string $identifier, string $otp, int $minutes)
    {
        return Otp::updateOrCreate(
            ['email_address' => $identifier],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes($minutes)
            ]
        );
    }

    public function getValidOtp(string $identifier)
    {
        return Otp::where('email_address', $identifier)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function verifyOtp(string $identifier, string $otp)
    {
        return Otp::where('email_address', $identifier)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();
    }
}
