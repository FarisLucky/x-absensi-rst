<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AccIzinNotification extends Notification
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
        $nama = $this->data['izin']->nama;
        $izin = $this->data['izin']->izin;
        $ket = $this->data['izin']->ket;
        $body = "Nama: {$nama}. Keterangan: {$ket}";

        return (new FcmMessage())
            ->setNotification((
                    new FcmNotification())
                    ->setTitle($izin)
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
