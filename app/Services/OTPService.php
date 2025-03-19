<?php

namespace App\Services;

use App\Models\OTP;
use App\Models\User;
use App\Notifications\SendOTPNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response;

class OTPService
{
    /**
     * Default OTP expiry time in minutes
     */
    private const DEFAULT_EXPIRY_MINUTES = 15;

    /**
     * Generate and send OTP to user
     *
     * @param User $user
     * @param string $purpose
     * @return string
     */
    public function generateAndSendOTP(User $user, string $purpose): string
    {
        return DB::transaction(function () use ($user, $purpose) {
            // Invalidate any existing OTPs for this user and purpose
            OTP::query()->where('user_id', $user->id)
                ->where('purpose', $purpose)
                ->where('used_at', null)
                ->update(['used_at' => now()]);

            // Generate new OTP
            $otp = $this->generateOTP();

            // Store OTP in database
            OTP::query()->create([
                'user_id' => $user->id,
                'otp' => $otp,
                'purpose' => $purpose,
                'expires_at' => Carbon::now()->addMinutes(self::DEFAULT_EXPIRY_MINUTES),
            ]);

            // Send OTP to user
            $user->notify(new SendOTPNotification($otp, $purpose));

            return $otp;
        });
    }

    /**
     * Generate a random OTP
     *
     * @param int $length
     * @return string
     * @throws RandomException
     */
    private function generateOTP(int $length = 6): string
    {
        return str_pad((string)random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Verify OTP
     *
     * @param User $user
     * @param string $otp
     * @param string $purpose
     * @return bool
     * @throws Exception
     */
    public function verifyOTP(User $user, string $otp, string $purpose): bool
    {
        $otpRecord = OTP::query()->where('user_id', $user->id)
            ->where('otp', $otp)
            ->where('purpose', $purpose)
            ->where('used_at', null)
            ->latest()
            ->first();

        if (!$otpRecord) {
            throw new Exception('Invalid OTP', Response::HTTP_BAD_REQUEST);
        }

        if ($otpRecord->isExpired()) {
            throw new Exception('OTP has expired', Response::HTTP_BAD_REQUEST);
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();

        return true;
    }
}
