<?php

namespace App\Observers;

use App\Models\Izin;
use App\Models\User;
use App\Notifications\AccIzinNotification;

class IzinObserver
{
    public function created(Izin $izin)
    {
        $user = User::where('nip', $izin->acc1)->first();

        foreach ($user->tokens() as $token) {
            if (!is_null(optional($token)->fcm_token)) {
                $user->notify(new AccIzinNotification([
                    'izin' => $izin,
                    'url' => 'https://gsabsen.my.id/izin-ku'
                ]));
            }
        }
    }
    public function updated(Izin $izin)
    {
        $user = null;

        if (
            !is_null($izin->acc1_at) && !is_null($izin->acc2)
        ) {
            $user = User::where('nip', $izin->acc2)->first();
        } else if (
            !is_null($izin->acc2_at) && !is_null($izin->acc3)
        ) {
            $user = User::where('nip', $izin->acc3)->first();
        } else if (
            $izin->jenis_table === Izin::KRS && !is_null($izin->acc1_at)
        ) {
            $user = User::where('nip', $izin->acc_sdm)->first();
        } else if (
            $izin->jenis_table === Izin::CUTI && (!is_null($izin->acc3) && !is_null($izin->acc3_at))
        ) {
            $user = User::where('nip', $izin->acc_sdm)->first();
        } else if (
            $izin->jenis_table === Izin::CUTI && (is_null($izin->acc3) && !is_null($izin->acc2_at))
        ) {
            $user = User::where('nip', $izin->acc_sdm)->first();
        } else if (
            $izin->jenis_table === Izin::CUTI && (is_null($izin->acc2) && !is_null($izin->acc1_at))
        ) {
            $user = User::where('nip', $izin->acc_sdm)->first();
        }

        if (!is_null(optional($user)->tokens())) {
            foreach ($user->tokens() as $token) {
                if (!is_null(optional($token)->fcm_token)) {
                    $user->notify(new AccIzinNotification([
                        'izin' => $izin,
                        'url' => 'https://gsabsen.my.id/izin-ku'
                    ]));
                }
            }
        }
    }
}
