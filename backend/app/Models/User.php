<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const SUPER_ADMIN = "SUPER_ADMIN";

    const STAFF = "STAF";

    const KEPALA = "KEPALA";

    const DIREKTUR = "DIREKTUR";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'nip',
        'password',
        'role',
        'device_hash',
        'platform',
        'model',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    public function getCreatedAtAttribute($key)
    {
        return date('Y-m-d', strtotime($key));
    }

    public function mKaryawan()
    {
        return $this->belongsTo(MKaryawan::class, 'nip', 'nip');
    }

    /**
     * third party function
     */

    // FCM
    public function routeNotificationForFcm(): string
    {
        // return "c_cTDXLmFv7OfEoP6P-eKV:APA91bG9HeagYfMuVf_u1CkBOPo3OH1h9w0Lgk-C39D25mbGBM8Cm7SLlrcStasisIOtTOgKs577jyJ7lin2fOJHc0X1WqBshPWRAc7reeKEsYt9PSa0sZ-K3yozHh7eRBsSGDeGnXzr";
        // return "fOCcn5llrkqdLfwwAqojMI:APA91bHHWvtqgEuc-XnbMosIqN_1zWX1IOUAtS0U2NT1bE_BWdEpesKdFKz4IZ4XEpg-x21SKZqcAKHkqTW0DxOcS_SWTKTP64jvQ_lKGWtmij9N4HqHy9Y";
        return (string) $this->currentAccessToken()->fcm_token;
    }
}
