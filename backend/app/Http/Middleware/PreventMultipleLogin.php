<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreventMultipleLogin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Cek apakah ada session lain untuk user ini
            $deviceId = $request->header('X-DEVICE-ID');
            $deviceType = $request->header('X-DEVICE-TYPE');

            if ($deviceType === 'web') {
                if ($deviceId !== $user->deviceweb_id) {
                    $user->currentAccessToken()->delete();

                    return response()->json([
                        'error' => 'Anda sudah login di perangkat lain. Silakan logout terlebih dahulu.'
                    ], 403);
                }
            } else if ($deviceType === 'apk') {
                if ($deviceId !== $user->deviceapk_id) {
                    $user->currentAccessToken()->delete();

                    return response()->json([
                        'error' => 'Anda sudah login di perangkat lain. Silakan logout terlebih dahulu.'
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
