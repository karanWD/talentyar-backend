<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Admin;
use App\Services\AdminPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends BaseApiController
{
    public function __construct(private readonly AdminPermissionService $permissions) {}

    /**
     * Login admin and issue sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::query()
            ->where('username', $validated['username'])
            ->where('status', 'active')
            ->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
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
                    'username' => $admin->username,
                    'phone' => $admin->phone,
                    'status' => $admin->status,
                    'roles' => $admin->getRoleNames()->values(),
                    'permissions' => $this->permissions->names($admin),
                    'abilities' => $this->permissions->abilities($admin),
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
                    'username' => $admin->username,
                    'phone' => $admin->phone,
                    'status' => $admin->status,
                    'roles' => $admin->getRoleNames()->values(),
                    'permissions' => $this->permissions->names($admin),
                    'abilities' => $this->permissions->abilities($admin),
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
