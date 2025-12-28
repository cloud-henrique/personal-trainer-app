<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * RegisterController
 *
 * Handles tenant (Personal Trainer) registration.
 * Creates both the tenant and the admin user in a single transaction.
 */
class RegisterController extends Controller
{
    public function __construct(
        protected TenantService $tenantService
    ) {}

    /**
     * Register a new tenant with admin user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        try {
            // Create tenant and admin user
            $result = $this->tenantService->createTenantWithAdmin($request->validated());

            // Log registration
            Log::channel('auth')->info('Tenant registered', [
                'tenant_id' => $result['tenant']->id,
                'email' => $result['tenant']->email,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $result['token'],
                    'user' => new UserResource($result['user']),
                    'tenant' => [
                        'id' => $result['tenant']->id,
                        'name' => $result['tenant']->name,
                        'slug' => $result['tenant']->slug,
                        'primary_color' => $result['tenant']->primary_color,
                        'logo_url' => $result['tenant']->logo_url,
                        'plan' => $result['tenant']->plan,
                    ],
                ],
                'message' => 'Cadastro realizado com sucesso',
            ], 201);

        } catch (\Exception $e) {
            Log::channel('auth')->error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar cadastro',
            ], 500);
        }
    }
}
