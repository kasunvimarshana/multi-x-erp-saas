<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8|confirmed',
                'tenant_slug' => 'required|string|exists:tenants,slug',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            // Get tenant
            $tenant = Tenant::where('slug', $request->tenant_slug)->first();

            if (!$tenant || !$tenant->is_active) {
                return $this->errorResponse('Invalid or inactive tenant', 400);
            }

            // Check if user already exists for this tenant
            $existingUser = User::where('email', $request->email)
                ->where('tenant_id', $tenant->id)
                ->first();

            if ($existingUser) {
                return $this->errorResponse('User already exists for this tenant', 409);
            }

            // Create user
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            // Create token
            $token = $user->createToken('auth-token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'User registered successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Login user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
                'tenant_slug' => 'required|string|exists:tenants,slug',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            // Get tenant
            $tenant = Tenant::where('slug', $request->tenant_slug)->first();

            if (!$tenant || !$tenant->is_active) {
                return $this->errorResponse('Invalid or inactive tenant', 400);
            }

            // Find user by email and tenant
            $user = User::where('email', $request->email)
                ->where('tenant_id', $tenant->id)
                ->first();

            if (!$user) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            if (!$user->is_active) {
                return $this->errorResponse('User account is not active', 403);
            }

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            // Create token
            $token = $user->createToken('auth-token')->plainTextToken;

            return $this->successResponse([
                'user' => $user->load('roles.permissions'),
                'token' => $token,
            ], 'Login successful');

        } catch (\Exception $e) {
            return $this->errorResponse('Login failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(null, 'Logout successful');

        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get current authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load(['roles.permissions', 'tenant']);

            return $this->successResponse($user);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Refresh token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $token = $request->user()->createToken('auth-token')->plainTextToken;

            return $this->successResponse([
                'token' => $token,
            ], 'Token refreshed successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Token refresh failed: ' . $e->getMessage(), 500);
        }
    }
}
