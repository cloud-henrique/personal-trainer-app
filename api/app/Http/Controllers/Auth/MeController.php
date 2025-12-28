<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * MeController
 *
 * Returns authenticated user data with tenant information.
 */
class MeController extends Controller
{
    /**
     * Get authenticated user data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
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
        ]);
    }
}
