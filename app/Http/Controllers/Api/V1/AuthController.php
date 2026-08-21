<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = DB::transaction(function () use ($data, $request) {
            $organization = Organization::create([
                'name' => $data['organization_name'],
                'domain' => $data['domain'] ?? null,
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'UTC',
                ],
                'is_active' => true,
            ]);

            $user = User::create([
                'organization_id' => $organization->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'status' => 'active',
                'last_login_at' => now(),
            ]);

            $user->assignRole('OrgAdmin');

            $token = $user->createToken(
                $request->input('device_name', 'auth-token'),
                $user->getAllPermissions()->pluck('name')->toArray()
            )->plainTextToken;

            return [
                'user' => $user->load('organization', 'roles', 'permissions'),
                'token' => $token,
            ];
        });

        return response()->json([
            'message' => 'Registration successful',
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        /** @var User|null $user */
        $user = User::with('organization', 'roles', 'permissions')
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account is '.$user->status.'. Please contact your administrator.',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->update(['last_login_at' => now()]);

        $abilities = $user->getAllPermissions()->pluck('name')->toArray();
        if (empty($abilities)) {
            $abilities = ['*'];
        }

        $token = $user->createToken(
            $credentials['device_name'] ?? 'auth-token',
            $abilities
        )->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->loadMissing('organization', 'roles', 'permissions');

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user->fresh(['organization', 'roles', 'permissions'])),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }
}
