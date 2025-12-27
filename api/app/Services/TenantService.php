<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * TenantService
 *
 * Service responsible for tenant creation and management.
 * Handles the complete tenant registration flow.
 */
class TenantService
{
    /**
     * Create a new tenant with admin user.
     *
     * @param array $data Tenant and admin user data
     * @return array Returns ['tenant' => Tenant, 'user' => User, 'token' => string]
     * @throws \Exception
     */
    public function createTenantWithAdmin(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Create tenant
            $tenant = Tenant::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'plan' => 'free',
                'is_active' => true,
            ]);

            // Create admin user for this tenant
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'role' => 'admin',
                'is_active' => true,
            ]);

            // Generate Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'tenant' => $tenant,
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    /**
     * Update tenant settings.
     *
     * @param Tenant $tenant
     * @param array $data
     * @return Tenant
     */
    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);
        return $tenant->fresh();
    }

    /**
     * Deactivate a tenant.
     *
     * @param Tenant $tenant
     * @return bool
     */
    public function deactivateTenant(Tenant $tenant): bool
    {
        return $tenant->update(['is_active' => false]);
    }

    /**
     * Activate a tenant.
     *
     * @param Tenant $tenant
     * @return bool
     */
    public function activateTenant(Tenant $tenant): bool
    {
        return $tenant->update(['is_active' => true]);
    }
}
