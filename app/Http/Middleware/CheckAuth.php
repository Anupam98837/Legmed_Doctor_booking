<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckAuth
{
    /** tokenable_type stored in personal_access_tokens */
    private const USER_TYPE = 'App\\Models\\User';

    /**
     * Only validates token + active user
     * If valid, passes user details to controller via request attributes
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1) Extract Bearer token
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $hashedToken = hash('sha256', $token);

        // 2) Validate token
        $pat = DB::table('personal_access_tokens')
            ->where('token', $hashedToken)
            ->where('tokenable_type', self::USER_TYPE)
            ->first();

        if (!$pat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        // 3) Check token expiry
        if (!empty($pat->expires_at)) {
            try {
                if (Carbon::parse($pat->expires_at)->isPast()) {
                    DB::table('personal_access_tokens')
                        ->where('id', $pat->id)
                        ->delete();

                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Unauthorized',
                    ], 401);
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }
        }

        // 4) Fetch user
        $user = DB::table('users')
            ->where('id', $pat->tokenable_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        if (isset($user->status) && $user->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        // 5) Pass authenticated user details to controller
        $request->attributes->set('auth_user', $user);
        $request->attributes->set('auth_user_id', (int) $user->id);
        $request->attributes->set('auth_user_uuid', (string) ($user->uuid ?? ''));
        $request->attributes->set('auth_name', (string) ($user->name ?? ''));
        $request->attributes->set('auth_email', (string) ($user->email ?? ''));
        $request->attributes->set('auth_role', (string) ($user->role ?? ''));
        $request->attributes->set('auth_tokenable_type', self::USER_TYPE);
        $request->attributes->set('auth_tokenable_id', (int) $user->id);

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (stripos($header, 'Bearer ') === 0) {
            $token = trim(substr($header, 7));
        } else {
            $token = trim($header);
        }

        return $token !== '' ? $token : null;
    }
}