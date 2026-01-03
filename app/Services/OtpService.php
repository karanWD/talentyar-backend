<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * OTP expiration time in minutes
     */
    private const EXPIRATION_MINUTES = 5;
    public const OTP_DIGIT_LENGTH = 4;

    /**
     * Generate and store OTP for phone number
     *
     * @param string $phone
     * @return string
     */
    public function generate(string $phone): string
    {
        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, str_repeat('9', self::OTP_DIGIT_LENGTH)), self::OTP_DIGIT_LENGTH, '0', STR_PAD_LEFT);

        // Store in cache with phone as key, expires in 5 minutes
        Cache::put($this->getCacheKey($phone), $otp, now()->addMinutes(self::EXPIRATION_MINUTES));

        // Log OTP for development (remove in production or use proper SMS service)
        Log::info("OTP generated for {$phone}: {$otp}");

        return $otp;
    }

    /**
     * Verify OTP for phone number
     *
     * @param string $phone
     * @param string $otp
     * @return bool
     */
    public function verify(string $phone, string $otp): bool
    {
        $cachedOtp = Cache::get($this->getCacheKey($phone));

        if (!$cachedOtp) {
            return false;
        }

        // Verify OTP matches
        if ($cachedOtp !== $otp) {
            return false;
        }

        // Delete OTP after successful verification
        Cache::forget($this->getCacheKey($phone));

        return true;
    }

    /**
     * Check if OTP exists for phone number
     *
     * @param string $phone
     * @return bool
     */
    public function exists(string $phone): bool
    {
        return Cache::has($this->getCacheKey($phone));
    }

    /**
     * Get cache key for phone number
     *
     * @param string $phone
     * @return string
     */
    private function getCacheKey(string $phone): string
    {
        return "otp:{$phone}";
    }
}

