<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SessionService
{
    public static function getSessionData()
    {
        $sessionId = Session::getId();

        return DB::table('sessions')->where('id', $sessionId)->first();
    }
}
