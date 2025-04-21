<?php

namespace App\Notifications;

use App\Models\TukarJadwal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TukarJadwalNotification extends Notification
{
    use Queueable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $pihak1 = $this->data['tukarJadwal']->nama_pihak1;
        $pihak2 = $this->data['tukarJadwal']->nama_pihak2;
        $tgl = $this->data['tukarJadwal']->tgl_pihak1_cast;
        $title = $this->data['tukarJadwal']->jenis == TukarJadwal::TUKAR_SHIFT ? 'TUKAR SHIFT' : 'TUKAR OFF';
        $body = "PIhak1: {$pihak1} dengan {$pihak2}. Tanggal: {$tgl}";

        return (new FcmMessage())
            ->setNotification((
                    new FcmNotification())
                    ->setTitle($title)
                    ->setBody($body)
            )
            ->setData(['click_action' => $this->data['url']]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
