<?php

namespace App\Utils;

use App\Models\Jadwal;
use App\Models\User;

class CheckRole
{
    public static function checkAtasanSemua(string $role)
    {
        return in_array($role, [User::KASUB, User::KABID, User::SUPER_ADMIN, User::DIREKTUR]);
    }
}
