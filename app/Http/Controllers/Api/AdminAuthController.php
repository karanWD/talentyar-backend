<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends BaseApiController
{
    /**
     * Login admin and issue sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'regex:/^09[0-9]{9}$/'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::query()
            ->where('phone', $validated['phone'])
            ->where('status', 'active')
            ->first();

        if (!$admin || !Hash::check($validated['password'], $admin->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $token = $admin->createToken(
            name: 'api-token-admin',
            abilities: ['*'],
            expiresAt: now()->addDays(30)
        )->plainTextToken;

        return $this->successResponse(
            [
                'token' => $token,
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'phone' => $admin->phone,
                    'status' => $admin->status,
                ],
            ],
            'Login successful'
        );
    }

    /**
     * Get authenticated admin profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $admin = $request->user('api-admin');

        return $this->successResponse(
            [
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'phone' => $admin->phone,
                    'status' => $admin->status,
                ],
            ],
            'Profile retrieved successfully'
        );
    }

    /**
     * Logout admin (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user('api-admin')->currentAccessToken()?->delete();

        return $this->successResponse(
            null,
            'Logged out successfully'
        );
    }
}
