<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    /**
     * Show login portal / landing page.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('app.dashboard');
        }

        return view('welcome');
    }

    /**
     * Process Web Login request.
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($validated, (bool) $request->boolean('remember', true))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            // Create or get Sanctum token for client-side API calls
            $token = $user->createToken('web-session-token', $user->getAllPermissions()->pluck('name')->toArray())->plainTextToken;

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Login successful',
                    'redirect_url' => route('app.dashboard'),
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->getRoleNames()->first() ?? 'User',
                        'organization' => $user->organization->name,
                    ],
                ]);
            }

            return redirect()->intended(route('app.dashboard'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Invalid credentials provided.',
                'errors' => ['email' => ['Invalid credentials provided.']],
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Process Tenant Registration request.
     */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $org = Organization::create([
                'name' => $validated['organization_name'],
                'settings' => ['currency' => 'USD', 'timezone' => 'UTC'],
                'is_active' => true,
            ]);

            $user = User::create([
                'organization_id' => $org->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ]);

            $user->assignRole('OrgAdmin');

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        $token = $user->createToken('web-session-token', $user->getAllPermissions()->pluck('name')->toArray())->plainTextToken;

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registration successful',
                'redirect_url' => route('app.dashboard'),
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'OrgAdmin',
                    'organization' => $user->organization->name,
                ],
            ]);
        }

        return redirect()->route('app.dashboard');
    }

    /**
     * Switch role / impersonate demo account for instant role testing.
     */
    public function switchRole(Request $request): RedirectResponse|JsonResponse
    {
        $role = $request->input('role', 'OrgAdmin');

        $roleEmailMap = [
            'SuperAdmin' => 'superadmin@crm-enterprise.local',
            'OrgAdmin' => 'admin@acmecorp.com',
            'SalesManager' => 'salesmanager@acmecorp.com',
            'SalesRepresentative' => 'salesrep@acmecorp.com',
        ];

        $targetEmail = $roleEmailMap[$role] ?? 'admin@acmecorp.com';
        $user = User::where('email', $targetEmail)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            $token = $user->createToken('web-session-token', $user->getAllPermissions()->pluck('name')->toArray())->plainTextToken;

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => "Switched to {$role}",
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->getRoleNames()->first() ?? $role,
                        'organization' => $user->organization->name,
                    ],
                ]);
            }
        }

        return redirect()->route('app.dashboard');
    }

    /**
     * Log out of the web session.
     */
    public function logout(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            $user->tokens()->where('name', 'web-session-token')->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
