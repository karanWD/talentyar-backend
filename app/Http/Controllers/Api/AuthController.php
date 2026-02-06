<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CheckUsernameRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    public function __construct(
        private OtpService $otpService
    ) {}

    /**
     * Request OTP for phone number
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
        ]);

        $phone = $validated['phone'];
        // Rate limiting: max 3 requests per 5 minutes per phone
        // TODO :: correct rate limit
         $key = 'otp_request:' . $phone;
         if (RateLimiter::tooManyAttempts($key, 300)) {
             $seconds = RateLimiter::availableIn($key);
             return response()->json([
                 'message' => 'Too many OTP requests. Please try again in ' . ceil($seconds / 60) . ' minutes.',
             ], 429);
         }

         RateLimiter::hit($key, 300); // 5 minutes

        // Generate OTP
        $otp = $this->otpService->generate($phone);

        // In production, send OTP via SMS service here
        // For now, it's logged (see OtpService)

        return $this->successResponse(
            [
                // Remove this in production - only for development
                'otp' => config('app.debug') ? $otp : null,
            ],
            'OTP sent successfully'
        );
    }

    /**
     * Verify OTP and login user
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'otp' => ['required', 'string', 'size:'.OtpService::OTP_DIGIT_LENGTH],
        ]);

        $phone = $validated['phone'];
        $otp = $validated['otp'];

        // Verify OTP
        if (!$this->otpService->verify($phone, $otp)) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid or expired OTP.'],
            ]);
        }

        $firstUserExists = User::where('phone', $phone)->exists();


        // Find or create user
        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'full_name' => '',
            ]
        );


        $profileCompleted = $user->full_name;
        // Revoke all existing tokens (optional - for single device login)
        // $user->tokens()->delete();

        // detect first user

        // Create new token
        $token = $user->createToken(
            name: 'api-token-user',
            abilities: ['*'],
            expiresAt: now()->addDays(30)
        )->plainTextToken;

        return $this->successResponse(
            [
                'token' => $token,
                'user' => new UserResource($user),
                'first_user' => !$firstUserExists || !$profileCompleted,
            ],
            'Login successful'
        );
    }

    /**
     * Get authenticated user
     */
    public function getProfile(Request $request): JsonResponse
    {
        return $this->successResponse(
            ['user' => new UserResource($request->user())],
            'User retrieved successfully'
        );
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            null,
            'Logged out successfully'
        );
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return $this->successResponse(
            null,
            'Logged out from all devices successfully'
        );
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return $this->successResponse(
            ['user' => new UserResource($user)],
            'Profile updated successfully'
        );
    }

    /**
     * Check if username is available (unique for other users).
     * Throttled to prevent abuse.
     */
    public function checkUsername(CheckUsernameRequest $request): JsonResponse
    {
        $username = $request->validated('username');
        $currentUser = $request->user();

        $takenByOther = User::where('username', $username)
            ->where('id', '!=', $currentUser->id)
            ->exists();

        return $this->successResponse(
            [
                'username' => $username,
                'available' => !$takenByOther,
            ],
            $takenByOther ? 'Username is already taken' : 'Username is available'
        );
    }
}
