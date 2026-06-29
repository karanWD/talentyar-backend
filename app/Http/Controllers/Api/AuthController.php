<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CheckUsernameRequest;
use App\Http\Requests\UpdateProfileAvatarRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserFollowResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        try {
            $url = env('KAVENEGAR_API_KEY','https://api.kavenegar.com/v1/573138633744656C484973353862484B4F4A474A67306671527A48577461426F4F7753445A4849483747303D/verify/lookup.json');
            $data = [
                'receptor' => $phone,
                'sender' => env('KAVENEGAR_SENDER','2000660110'),
                'template' => env('KAVENEGAR_TEMPLATE','OTP-CODE'),
                'token' => $otp,
            ];

            $response =  Http::asForm()->post($url, $data);
            Log::info('OTP', ['response' => $response->body()]);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }


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

//        if ($otp != 1111){
//            throw ValidationException::withMessages([
//                'otp' => ['Invalid or expired OTP.'],
//            ]);
//        }

        $firstUserExists = User::where('phone', $phone)->exists();


        // Find or create user
        $user = User::firstOrCreate(
            ['phone' => $phone]
        );

        $user->ensureReferralCode();


        // Revoke all existing tokens (optional - for single device login)
        // $user->tokens()->delete();

        // detect first user

        // Create new token
        $token = $user->createToken(
            name: 'api-token-user',
            abilities: ['*'],
            expiresAt: now()->addDays(30)
        )->plainTextToken;

        $talentYarUser = User::query()
            ->where('username','talentyar')
            ->first();

        if($talentYarUser) {
            if (!$user->following()->where('following_id', $talentYarUser->id)->exists()) {
                $user->following()->attach($talentYarUser->id);
            }
        }

        return $this->successResponse(
            [
                'token' => $token,
                'user' => new UserResource($user),
                'first_user' => !$firstUserExists,
            ],
            'Login successful'
        );
    }

    /**
     * Refresh the current token: revoke it and issue a new one.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $token = $user->createToken(
            name: 'api-token-user',
            abilities: ['*'],
            expiresAt: now()->addDays(30)
        )->plainTextToken;

        return $this->successResponse(
            [
                'token' => $token,
                'user' => new UserResource($user),
            ],
            'Token refreshed successfully'
        );
    }

    /**
     * Get authenticated user
     */
    public function getProfile(Request $request): JsonResponse
    {
        return $this->successResponse(
            ['user' => new UserResource($request->user()->load('media'))],
            'User retrieved successfully'
        );
    }

    /**
     * Get authenticated user's referral code.
     */
    public function getReferralCode(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->ensureReferralCode();
        $user->loadCount('referrals');

        return $this->successResponse(
            [
                'referral_code' => $user->referral_code,
                'referrals_count' => (int) $user->referrals_count,
            ],
            'Referral code retrieved successfully'
        );
    }

    /**
     * Apply another user's referral code for the authenticated user.
     */
    public function useReferralCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'referral_code' => ['required', 'string', 'size:8'],
        ]);

        $user = $request->user();

        if ($user->referred_by_user_id) {
            return $this->errorResponse('Referral code has already been used for this user', 422);
        }

        $referralCode = strtolower($validated['referral_code']);
        $referrer = User::query()
            ->where('referral_code', $referralCode)
            ->first();

        if (! $referrer) {
            return $this->errorResponse('Referral code is invalid', 422);
        }

        if ($referrer->id === $user->id) {
            return $this->errorResponse('You cannot use your own referral code', 422);
        }

        $user->forceFill([
            'referred_by_user_id' => $referrer->id,
        ])->save();

        $referrer->loadCount('referrals');

        return $this->successResponse(
            [
                'referral_code' => $referrer->referral_code,
                'referred_by_user_id' => $referrer->id,
                'referrals_count' => (int) $referrer->referrals_count,
            ],
            'Referral code applied successfully'
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

        $user->load('media');

        return $this->successResponse(
            ['user' => new UserResource($user)],
            'Profile updated successfully'
        );
    }

    public function updateProfileAvatar(UpdateProfileAvatarRequest $request)
    {
        $user = $request->user();
        $user->unAssignMedia('avatar');
        $videoMedia = Media::where('hash', $request->validated('profile_hash'))->first();
        $user->assignMedia($videoMedia, 'avatar');
        $user->load('media');

        return $this->successResponse(
            ['user' => new UserResource($user)],
            'Profile updated successfully',
            201
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
