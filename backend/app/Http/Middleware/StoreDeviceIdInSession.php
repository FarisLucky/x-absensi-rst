<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class StoreDeviceIdInSession
{
    public function handle($request, Closure $next)
    {
        $deviceId = $request->header('X-Device-ID');
        $deviceType = $request->header('X-Device-TYPE');

        if (Auth::check() && $deviceId) {
            $sessionId = Session::getId();

            DB::table('sessions')
                ->where('id',  $sessionId)
                ->update([
                    'device_id' => $deviceId,
                    'device_type' => $deviceType
                ]);
        }

        return $next($request);
    }
}
