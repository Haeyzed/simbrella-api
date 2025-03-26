<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\LoginHistory;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    private const DEFAULT_TOKEN_TTL = 10080;

    /**
     * @var OTPService
     */
    private OTPService $otpService;

    /**
     * AuthService constructor.
     *
     * @param OTPService $otpService
     */
    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Register a new user.
     *
     * @param array $userData
     * @param array $roles
     * @param array $permissions
     * @return array
     */
    public function register(array $userData, array $roles = [], array $permissions = []): array
    {
        $userData['password'] = Hash::make($userData['password']);

        $user = DB::transaction(function () use ($userData, $roles, $permissions) {
            $user = User::query()->create($userData);
            if (!empty($roles)) $user->assignRole($roles);
            if (!empty($permissions)) $user->givePermissionTo($permissions);
            return $user;
        });

        event(new Registered($user));

        // Send verification OTP
        $this->otpService->generateAndSendOTP($user, 'email_verification');

        return ['user' => $user];
    }

    /**
     * Attempt user login.
     *
     * @param array $credentials User credentials
     * @return array Contains login status and data
     *
     * @throws Exception When login fails
     */
    public function login(array $credentials): array
    {
        auth()->factory()->setTTL(self::DEFAULT_TOKEN_TTL);

        if (!$token = auth()->attempt($credentials)) {
            throw new Exception('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        $user = auth()->user();

        if ($user->status != StatusEnum::ACTIVE) {
            auth()->logout();
            throw new Exception('Your account is not active. Please contact administration.', Response::HTTP_FORBIDDEN);
        }

        // Record login history
        if (class_exists(LoginHistory::class)) {
            LoginHistory::query()->create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Logout user.
     *
     * @return void
     */
    public function logout(): void
    {
        auth()->logout();
    }

    /**
     * Send password reset OTP.
     *
     * @param string $email
     * @return User
     * @throws Exception
     */
    public function sendPasswordResetOTP(string $email): User
    {
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            throw new Exception('User not found', Response::HTTP_NOT_FOUND);
        }

        $this->otpService->generateAndSendOTP($user, 'password_reset');

        return $user;
    }

    /**
     * Reset user password with OTP.
     *
     * @param string $email
     * @param string $otp
     * @param string $password
     * @return User
     * @throws Exception
     */
    public function resetPasswordWithOTP(string $email, string $otp, string $password): User
    {
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            throw new Exception('User not found', Response::HTTP_NOT_FOUND);
        }

        // Verify OTP
        $this->otpService->verifyOTP($user, $otp, 'password_reset');

        // Update password
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        return $user;
    }

    /**
     * Change user password.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @throws Exception
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new Exception('Current password is incorrect', Response::HTTP_BAD_REQUEST);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }

    /**
     * Verify user email with OTP.
     *
     * @param User $user
     * @param string $otp
     * @throws Exception
     */
    public function verifyEmailWithOTP(User $user, string $otp): void
    {
        // Verify OTP
        $this->otpService->verifyOTP($user, $otp, 'email_verification');

        // Mark email as verified
        if (!$user->hasVerifiedEmail()) {
            $user->email_verified_at = now();
            $user->save();
        }
    }

    /**
     * Resend verification OTP.
     *
     * @param User $user
     * @throws Exception
     */
    public function resendVerificationOTP(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw new Exception('Email already verified', Response::HTTP_BAD_REQUEST);
        }

        $this->otpService->generateAndSendOTP($user, 'email_verification');
    }

    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (isset($data['email']) && $data['email'] !== $user->email) {
                if (User::where('email', $data['email'])->exists()) {
                    throw new Exception('Email already in use', Response::HTTP_BAD_REQUEST);
                }
                $user->email = $data['email'];
                $user->email_verified_at = null;

                // Send verification OTP for new email
                $this->otpService->generateAndSendOTP($user, 'email_verification');
            }

            $user->fill($data);
            $user->save();

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            if (isset($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            return $user->fresh(['roles', 'permissions']);
        });
    }
}
