<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * LogoutController
 *
 * Handles user logout by revoking the current access token.
 */
class LogoutController extends Controller
{
    /**
     * Logout user by revoking current token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Log logout
            Log::channel('auth')->info('User logged out', [
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso',
            ]);

        } catch (\Exception $e) {
            Log::channel('auth')->error('Logout failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar logout',
            ], 500);
        }
    }
}
