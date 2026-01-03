<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Employer\UploadMediaRequest;
use App\Http\Resources\EmployerResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Employer;
use App\Models\User;
use App\Services\MediaService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Traits\MediaTrait;

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

        $firstEmployerExists = Employer::where('phone', $phone)->exists();


        // Find or create user
        $employer = Employer::firstOrCreate(
            ['phone' => $phone],
            [
                'full_name' => '',
            ]
        );


        // Revoke all existing tokens (optional - for single device login)
        // $employer->tokens()->delete();

        // detect first user

        // Create new token
        $token = $employer->createToken(
            name: 'api-token-employer',
            abilities: ['*'],
            expiresAt: now()->addDays(7)
        )->plainTextToken;

        return $this->successResponse(
            [
                'token' => $token,
                'employer' => new EmployerResource($employer),
                'first_employer' => !$firstEmployerExists,
                'employer_profile_complete' => !is_null($employer->full_name),
                'has_company' => !is_null($employer->company_id),
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
            ['employer' => new EmployerResource($request->user())],
            'User retrieved successfully'
        );
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke current token
        $request->user('api-employer')->currentAccessToken()->delete();

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
        $request->user('api-employer')->tokens()->delete();

        return $this->successResponse(
            null,
            'Logged out from all devices successfully'
        );
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'tel' => ['nullable', 'string'],
            'organizational_position' => ['nullable', 'string'],
        ]);

        $employer = $request->user();
        $employer->update($validated);

        return $this->successResponse(
            ['employer' => new EmployerResource($employer)],
            'Profile updated successfully'
        );
    }

    public function uploadMedia(UploadMediaRequest $request): JsonResponse
    {
        $employer = auth('api-employer')->user();
        $res = [];
        foreach ($request->validated()['files'] as $item){
            $mediaService = new MediaService();
            $media = $mediaService->addMediaFromUploadedFile($item,$request->get('entity_slug'),$request->get('type'));
            $res[] = new MediaResource($media);
        }
        return $this->successResponse([
            'media' => $res,
        ], 'Media uploaded successfully');
    }
}
