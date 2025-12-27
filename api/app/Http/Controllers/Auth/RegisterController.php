<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:tenants,email',
                'password' => 'required|string|min:8|confirmed',
                'slug' => 'required|string|alpha_dash|unique:tenants,slug',
                'phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Create tenant and admin user
            $result = $this->tenantService->createTenantWithAdmin($request->all());

            // Log registration
            Log::channel('auth')->info('Tenant registered', [
                'tenant_id' => $result['tenant']->id,
                'email' => $result['tenant']->email,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $result['token'],
                    'user' => [
                        'id' => $result['user']->id,
                        'name' => $result['user']->name,
                        'email' => $result['user']->email,
                        'role' => $result['user']->role,
                    ],
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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

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
