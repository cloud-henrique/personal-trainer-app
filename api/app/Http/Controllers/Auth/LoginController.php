<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * LoginController
 *
 * Handles user authentication and returns token + tenant data for dynamic theming.
 */
class LoginController extends Controller
{
    /**
     * Authenticate user and return token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function store(LoginRequest $request): JsonResponse
    {
        try {
            // Find user by email (across all tenants)
            $user = User::withoutGlobalScope('tenant')
                ->where('email', $request->email)
                ->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas',
                ], 401);
            }

            // Check if user is active
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário inativo',
                ], 403);
            }

            // Load tenant
            $tenant = $user->tenant;

            // Check if tenant is active
            if (!$tenant->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conta do personal trainer inativa',
                ], 403);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Log login
            Log::channel('auth')->info('User logged in', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'avatar_url' => $user->avatar_url,
                    ],
                    'tenant' => [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'slug' => $tenant->slug,
                        'primary_color' => $tenant->primary_color,
                        'logo_url' => $tenant->logo_url,
                        'cover_url' => $tenant->cover_url,
                        'plan' => $tenant->plan,
                    ],
                ],
                'message' => 'Login realizado com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('auth')->error('Login failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar login',
            ], 500);
        }
    }
}
