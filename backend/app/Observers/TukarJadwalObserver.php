<?php

namespace App\Observers;

use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\TukarJadwal;
use App\Models\User;
use App\Notifications\TukarJadwalNotification;

class TukarJadwalObserver
{
    public function created(TukarJadwal $tukarJadwal)
    {
        $user = User::where('nip', $tukarJadwal->nip_pihak2)->first();

        if (!is_null(optional($user)->tokens())) {
            foreach ($user->tokens() as $token) {
                if (!is_null(optional($token)->fcm_token)) {
                    $user->notify(new TukarJadwalNotification([
                        'tukarJadwal' => $tukarJadwal,
                        'url' => 'https://gsabsen.my.id/tukar-shift'
                    ]));
                }
            }
        }
    }

    public function updated(TukarJadwal $tukarJadwal)
    {
        $user = null;
        if (
            !is_null($tukarJadwal->acc_by) && is_null($tukarJadwal->acc_by_at) && !is_null($tukarJadwal->acc_pihak2)
        ) {
            $user = User::where('nip', $tukarJadwal->acc_by)->first();
        } else if (
            !is_null($tukarJadwal->acc_by_at) && is_null($tukarJadwal->acc_at)
        ) {
            $sdm = MKaryawan::with('user')
                ->whereHas('jabatans', function ($query) {
                    $query->where('id_jabatan', MJabatan::SDM_ID);
                })
                ->first('nip');
            $user = User::where('nip', $sdm->nip)->first();
        }

        if (!is_null(optional($user)->tokens())) {
            foreach ($user->tokens() as $token) {
                if (!is_null(optional($token)->fcm_token)) {
                    $user->notify(new TukarJadwalNotification([
                        'tukarJadwal' => $tukarJadwal,
                        'url' => 'https://gsabsen.my.id/tukar-shift'
                    ]));
                }
            }
        }
    }
}
