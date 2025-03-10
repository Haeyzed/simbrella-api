<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordWithOTPRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\VerifyOTPRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ACLService;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Auth Authentication Controller
 *
 * Handles authentication-related actions for users.
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller implements HasMiddleware
{
    /**
     * @var ACLService
     */
    protected ACLService $ACLService;

    /**
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * AuthController constructor.
     *
     * @param ACLService $ACLService
     * @param AuthService $authService
     */
    public function __construct(ACLService $ACLService, AuthService $authService)
    {
        $this->ACLService = $ACLService;
        $this->authService = $authService;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login', 'register', 'forgotPassword', 'resetPasswordWithOTP', 'verifyEmail']),
        ];
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: array{
     *         user: UserResource
     *     }
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register(
                $request->validated(),
                $request->roles ?? [],
                $request->permissions ?? []
            );

            return response()->success(new UserResource($result['user']), 'User registered successfully. Verification OTP sent to your email.');
        } catch (Exception $e) {
            return response()->serverError('Failed to register user: ' . $e->getMessage());
        }
    }

    /**
     * Login get a JWT using the provided credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @response array{
     *     status: boolean,
     *     message: string,
     *     data: array{
     *         access_token: string,
     *         token_type: string,
     *         expires_in: int,
     *         user: UserResource
     *     }
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->only(['email', 'password']));
            return $this->respondWithToken($result['token']);
        } catch (Exception $e) {
            return response()->error('Failed to login: ' . $e->getMessage());
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        $user = auth()->user()->load(['roles', 'permissions']);
        return response()->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => new UserResource($user),
        ], 'Successfully logged in');
    }

    /**
     * Profile.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: UserResource
     * }
     */
    public function me(): JsonResponse
    {
        return response()->success(new UserResource(auth()->user()->load(['roles', 'permissions'])), 'User retrieved successfully.');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     * }
     */
    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->success(null, 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array{
     *          access_token: string,
     *          token_type: string,
     *          expires_in: int,
     *          user: UserResource
     *      }
     * }
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Send password reset OTP.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     * }
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->sendPasswordResetOTP($request->email);
            return response()->success(null, 'Password reset OTP sent to your email');
        } catch (Exception $e) {
            return response()->serverError('Failed to send reset OTP: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password with OTP.
     *
     * @param ResetPasswordWithOTPRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     * }
     */
    public function resetPasswordWithOTP(ResetPasswordWithOTPRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPasswordWithOTP(
                $request->email,
                $request->otp,
                $request->password
            );
            return response()->success(null, 'Password reset successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to reset password: ' . $e->getMessage());
        }
    }

    /**
     * Change user password.
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     * }
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->changePassword(
                auth()->user(),
                $request->current_password,
                $request->password
            );
            return response()->success(null, 'Password changed successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to change password: ' . $e->getMessage());
        }
    }

    /**
     * Verify user email with OTP.
     *
     * @param VerifyOTPRequest $request
     * @param string $email
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     * }
     */
    public function verifyEmail(VerifyOTPRequest $request, string $email): JsonResponse
    {
        try {
            $user = User::query()->where('email', $email)->firstOrFail();
            $this->authService->verifyEmailWithOTP($user, $request->otp);
            return response()->success(null, 'Email verified successfully.');
        } catch (Exception $e) {
            return response()->serverError('Failed to verify email: ' . $e->getMessage());
        }
    }

    /**
     * Resend email verification OTP.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     * }
     */
    public function resendVerification(): JsonResponse
    {
        try {
            $this->authService->resendVerificationOTP(auth()->user());
            return response()->success(null, 'Verification OTP resent successfully.');
        } catch (Exception $e) {
            return response()->serverError('Failed to send verification OTP: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile.
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: UserResource
     * }
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->updateProfile(auth()->user(), $request->validated());
            return response()->success(new UserResource($user), 'Profile updated successfully');
        } catch (Exception $e) {
            return response()->serverError('Failed to update profile: ' . $e->getMessage());
        }
    }
}
