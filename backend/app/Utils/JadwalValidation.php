<?php

namespace App\Utils;

use App\Models\Jadwal;

class JadwalValidation
{
    public static function notActive(Jadwal $jadwal)
    {
        if (now()->greaterThanOrEqualTo(\Carbon\Carbon::make('2024-08-01'))) {
            return is_null($jadwal->validate_at);
        }

        return false;
    }
}
